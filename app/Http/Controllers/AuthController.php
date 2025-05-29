<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            Log::info('Registration request: ' . json_encode($request->all()));
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|in:candidate,employer,student,admin,superadmin',
                'phone' => 'nullable|string|max:20',
                'course_completion_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'registration_status' => 'pending',
                'email_verified_at' => null,
                'phone' => $request->phone,
                'course_completion_year' => $request->course_completion_year,
            ]);

            Log::info('User created with role: ' . $user->role);

            try {
                $user->sendEmailVerificationNotification();
            } catch (\Exception $e) {
                Log::error('Email verification failed: ' . $e->getMessage());
            }

            $token = JWTAuth::fromUser($user);
            return response()->json([
                'message' => 'User registered successfully',
                'token' => $token,
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());
            return response()->json(['error' => 'Registration failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            Log::error('Login validation failed: ' . json_encode($validator->errors()));
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Log::info('Login attempt: ' . json_encode($request->only('email')));
        $credentials = $request->only('email', 'password');
        if (!$token = JWTAuth::attempt($credentials)) {
            $user = User::where('email', $request->email)->first();
            Log::error('Login failed', [
                'email' => $request->email,
                'user_exists' => !is_null($user),
                'email_verified' => $user ? !is_null($user->email_verified_at) : false,
                'registration_status' => $user ? $user->registration_status : 'none',
            ]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        Log::info('Login successful for email: ' . $request->email);
        return response()->json([
            'message' => 'Login successful',
            'token' => $token
        ], 200);
    }

    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'Successfully logged out'], 200);
        } catch (\Exception $e) {
            Log::error('Logout failed: ' . $e->getMessage());
            return response()->json(['error' => 'Logout failed'], 500);
        }
    }

    public function verifyEmail(Request $request, $id, $hash)
    {
        try {
            $user = User::findOrFail($id);

            if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
                Log::warning('Invalid verification link for user ID: ' . $id);
                return response()->json(['error' => 'Invalid verification link'], 400);
            }

            if ($user->hasVerifiedEmail()) {
                return response()->json(['message' => 'Email already verified'], 200);
            }

            $user->markEmailAsVerified();
            event(new \Illuminate\Auth\Events\Verified($user));
            Log::info('Email verified for user ID: ' . $id);

            return response()->json(['message' => 'Email verified successfully'], 200);
        } catch (\Exception $e) {
            Log::error('Email verification failed: ' . $e->getMessage());
            return response()->json(['error' => 'Verification failed', 'message' => $e->getMessage()], 500);
        }
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)], 200)
            : response()->json(['error' => __($status)], 400);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();
                event(new \Illuminate\Auth\Events\PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __($status)], 200)
            : response()->json(['error' => __($status)], 400);
    }
}
