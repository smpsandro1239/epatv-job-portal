<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RegistrationWindow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PendingRegistrationNotification;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
  public function register(Request $request)
  {
    Log::info('Raw input: ' . $request->getContent());
    Log::info('Parsed input: ' . json_encode($request->all()));
    // ... resto do código
    try {
      Log::info('Registration request: ' . json_encode($request->all()));
      $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'role' => 'required|in:candidate,employer,admin,superadmin',
        'window_password' => 'required_if:role,candidate',
        'phone' => 'nullable|string|max:20',
        'course_completion_year' => 'nullable|integer|min:1900|max:' . date('Y'),
        'company_name' => 'nullable|string|max:255',
        'company_city' => 'nullable|string|max:255',
        'company_website' => 'nullable|url',
        'company_description' => 'nullable|string',
      ]);

      if ($validator->fails()) {
        Log::error('Validation failed: ' . json_encode($validator->errors()));
        return response()->json(['errors' => $validator->errors()], 422);
      }

      $window = RegistrationWindow::where('is_active', true)
        ->where('start_time', '<=', now())
        ->where('end_time', '>=', now())
        ->first();

      $status = 'approved';
      if ($request->role === 'candidate') {
        $isValidWindow = $window && $window->current_registrations < $window->max_registrations;
        $isValidPassword = $window && Hash::check($request->window_password, $window->password) && now()->diffInHours($window->first_use_time) < 2;

        if (!$isValidWindow && !$isValidPassword) {
          $status = 'pending';
          Notification::send(User::where('role', 'superadmin')->get(), new PendingRegistrationNotification($request->all()));
        } else {
          if ($isValidPassword && !$window->first_use_time) {
            $window->update(['first_use_time' => now()]);
          }
          if ($window) {
            $window->increment('current_registrations');
          }
        }
      }

      $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $request->role,
        'registration_status' => $status,
        'email_verified_at' => null,
        'phone' => $request->phone,
        'course_completion_year' => $request->course_completion_year,
        'company_name' => $request->company_name,
        'company_city' => $request->company_city,
        'company_website' => $request->company_website,
        'company_description' => $request->company_description,
      ]);

      Log::info('User created with role: ' . $user->role . ', ID: ' . $user->id);

      try {
        $user->sendEmailVerificationNotification();
        if ($status === 'pending') {
          Notification::send($user, new PendingRegistrationNotification($user));
        }
      } catch (\Exception $e) {
        Log::error('Email notification failed: ' . $e->getMessage());
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
      return response()->json(['errors' => $validator->errors()], 422);
    }

    if (!$token = JWTAuth::attempt($request->only('email', 'password'))) {
      return response()->json(['error' => 'Unauthorized'], 401);
    }

    return response()->json(['token' => $token]);
  }

  public function verifyEmail(Request $request, $id, $hash)
  {
    $user = User::findOrFail($id);

    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
      return response()->json(['error' => 'Invalid verification link'], 400);
    }

    if ($user->hasVerifiedEmail()) {
      return response()->json(['message' => 'Email already verified'], 200);
    }

    $user->markEmailAsVerified();
    return response()->json(['message' => 'Email verified successfully'], 200);
  }

  public function logout(Request $request)
  {
    JWTAuth::invalidate(JWTAuth::getToken());
    return response()->json(['message' => 'Successfully logged out']);
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
      }
    );

    return $status === Password::PASSWORD_RESET
      ? response()->json(['message' => __($status)], 200)
      : response()->json(['error' => __($status)], 400);
  }
}
