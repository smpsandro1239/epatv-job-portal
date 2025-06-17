@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-6">Edit Employer Profile</h1>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Validation Error!</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('employer.profile.update') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Contact Person Name:</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <hr class="my-6">

        <div class="mb-4">
            <label for="company_name" class="block text-gray-700 text-sm font-bold mb-2">Company Name:</label>
            <input type="text" id="company_name" name="company_name" value="{{ old('company_name', $user->company_name) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="mb-4">
            <label for="company_city" class="block text-gray-700 text-sm font-bold mb-2">Company City:</label>
            <input type="text" id="company_city" name="company_city" value="{{ old('company_city', $user->company_city) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="mb-4">
            <label for="company_website" class="block text-gray-700 text-sm font-bold mb-2">Company Website:</label>
            <input type="url" id="company_website" name="company_website" value="{{ old('company_website', $user->company_website) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="mb-4">
            <label for="company_description" class="block text-gray-700 text-sm font-bold mb-2">Company Description:</label>
            <textarea id="company_description" name="company_description" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('company_description', $user->company_description) }}</textarea>
        </div>

        <div class="mb-6">
            <label for="company_logo" class="block text-gray-700 text-sm font-bold mb-2">Company Logo (Max 2MB):</label>
            <input type="file" id="company_logo" name="company_logo" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            @if($user->company_logo)
                <div class="mt-2">
                    <p class="text-sm text-gray-600">Current logo:</p>
                    <img src="{{ Storage::url($user->company_logo) }}" alt="Company Logo" style="max-width: 150px; max-height: 150px;" class="rounded mt-1">
                    <div class="mt-1">
                         <input type="checkbox" name="remove_company_logo" id="remove_company_logo" value="1">
                         <label for="remove_company_logo" class="text-xs text-red-600">Remove current logo</label>
                    </div>
                </div>
            @endif
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Update Profile
            </button>
            <a href="{{ route('employer.profile.show') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
