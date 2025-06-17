@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-6">Employer Profile</h1>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="mb-4">
            <strong class="block text-gray-700 text-sm font-bold mb-2">Contact Person:</strong>
            <p class="text-gray-700">{{ $user->name }}</p>
        </div>
        <div class="mb-4">
            <strong class="block text-gray-700 text-sm font-bold mb-2">Contact Email:</strong>
            <p class="text-gray-700">{{ $user->email }}</p>
        </div>
        <hr class="my-4">
        <div class="mb-4">
            <strong class="block text-gray-700 text-sm font-bold mb-2">Company Name:</strong>
            <p class="text-gray-700">{{ $user->company_name ?? 'Not provided' }}</p>
        </div>
        <div class="mb-4">
            <strong class="block text-gray-700 text-sm font-bold mb-2">Company City:</strong>
            <p class="text-gray-700">{{ $user->company_city ?? 'Not provided' }}</p>
        </div>
        <div class="mb-4">
            <strong class="block text-gray-700 text-sm font-bold mb-2">Company Website:</strong>
            @if($user->company_website)
                <a href="{{ $user->company_website }}" target="_blank" class="text-blue-500 hover:underline">{{ $user->company_website }}</a>
            @else
                <p class="text-gray-700">Not provided</p>
            @endif
        </div>
        <div class="mb-4">
            <strong class="block text-gray-700 text-sm font-bold mb-2">Company Description:</strong>
            <p class="text-gray-700">{{ $user->company_description ?? 'Not provided' }}</p>
        </div>

        @if($user->company_logo)
            <div class="mb-4">
                <strong class="block text-gray-700 text-sm font-bold mb-2">Company Logo:</strong>
                <img src="{{ Storage::url($user->company_logo) }}" alt="Company Logo" class="max-w-xs h-auto rounded">
            </div>
        @else
            <div class="mb-4">
                <strong class="block text-gray-700 text-sm font-bold mb-2">Company Logo:</strong>
                <p class="text-gray-700">No logo uploaded.</p>
            </div>
        @endif

        <div class="mt-6">
            <a href="{{ route('employer.profile.edit') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Edit Profile
            </a>
        </div>
    </div>
</div>
@endsection
