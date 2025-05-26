@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-bold">Dashboard</h2>
        <p>Welcome, {{ Auth::user()->name }}!</p>
        <a href="{{ route('logout') }}" class="text-blue-500">Logout</a>
    </div>
@endsection
