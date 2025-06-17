@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-screen -mt-16"> {{-- -mt-16 to offset potential navbar height if sticky --}}
    <div class="w-full max-w-md">
        <form method="POST" action="{{ route('login.store') }}" class="bg-white shadow-xl rounded-lg px-8 pt-6 pb-8 mb-4">
            @csrf
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Welcome Back!</h2>

            {{-- Email Input --}}
            <div class="mb-6">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor"><path d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                    </div>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                           class="shadow appearance-none border @error('email') border-red-500 @enderror rounded w-full py-3 px-4 pl-10 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="you@example.com">
                </div>
                @error('email')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password Input --}}
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                 <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                         <svg class="h-5 w-5 text-gray-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <input type="password" name="password" id="password" required
                           class="shadow appearance-none border @error('password') border-red-500 @enderror rounded w-full py-3 px-4 pl-10 text-gray-700 mb-3 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="••••••••••••">
                </div>
                @error('password')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember Me & Forgot Password --}}
            <div class="flex items-center justify-between mb-6">
                <label for="remember_me" class="flex items-center">
                    <input type="checkbox" id="remember_me" name="remember" class="form-checkbox h-4 w-4 text-blue-600 transition duration-150 ease-in-out">
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>
                @if (Route::has('password.request')) {{-- Assuming password reset routes are named 'password.request' --}}
                    <a href="{{ route('password.request') }}" class="text-sm text-blue-500 hover:text-blue-700 font-semibold">
                        Forgot password?
                    </a>
                @endif
            </div>

            {{-- Submit Button --}}
            <div class="flex items-center justify-center">
                <button type="submit"
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-4 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition duration-150">
                    Sign In
                </button>
            </div>

             <p class="text-center text-gray-500 text-xs mt-6">
                Don't have an account? <a href="{{ route('register') }}" class="text-blue-500 hover:text-blue-700 font-semibold">Sign up</a>.
            </p>
        </form>
        <p class="text-center text-gray-500 text-xs">
            &copy;{{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </div>
</div>
@endsection
