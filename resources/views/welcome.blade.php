@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold text-center">Welcome to EPATV Job Portal</h1>
        <p class="text-center mt-4">Find or post job opportunities tailored to your skills and interests.</p>
        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Login</a>
            <a href="{{ route('register') }}" class="bg-green-500 text-white px-4 py-2 rounded ml-2">Register</a>
        </div>
    </div>
@endsection
