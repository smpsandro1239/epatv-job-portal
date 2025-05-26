<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
  public function register(Request $request)
  {
    try {
      $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6|confirmed',
        'role' => 'required|in:candidate,employer',
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
      ]);

      try {
        $user->sendEmailVerificationNotification();
      } catch (\Exception $e) {
        Log::error('Email verification failed: ' . $e->getMessage());
      }

      return response()->json(['message' => 'User registered successfully', 'user' => $user], 201);
    } catch (\Exception $e) {
      Log::error('Registration failed: ' . $e->getMessage());
      return response()->json(['error' => 'Registration failed', 'message' => $e->getMessage()], 500);
    }
  }
}
