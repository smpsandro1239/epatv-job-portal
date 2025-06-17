@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white shadow-xl rounded-lg overflow-hidden">
        <div class="px-6 py-8 md:p-10">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-8 text-center">Edit Company Profile</h1>

            {{-- Global errors already handled by layouts.app --}}

            <form action="{{ route('employer.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Contact Information Section --}}
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Contact Information</h2>
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Contact Person Name <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                               class="shadow-sm appearance-none border @error('name') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('name') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                    </div>
                    {{-- Email is usually not editable directly on profile forms, or requires special handling --}}
                    <div class="mt-4">
                        <label for="email_display" class="block text-sm font-medium text-gray-700 mb-1">Contact Email (Cannot be changed here)</label>
                        <input type="email" id="email_display" name="email_display" value="{{ $user->email }}" disabled
                               class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-500 bg-gray-100 leading-tight focus:outline-none">
                    </div>
                </div>

                {{-- Company Details Section --}}
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Company Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">Company Name <span class="text-red-500">*</span></label>
                            <input type="text" id="company_name" name="company_name" value="{{ old('company_name', $user->company_name) }}" required
                                   class="shadow-sm appearance-none border @error('company_name') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('company_name') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="company_city" class="block text-sm font-medium text-gray-700 mb-1">Company City</label>
                            <input type="text" id="company_city" name="company_city" value="{{ old('company_city', $user->company_city) }}"
                                   class="shadow-sm appearance-none border @error('company_city') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('company_city') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="mt-6">
                        <label for="company_website" class="block text-sm font-medium text-gray-700 mb-1">Company Website</label>
                        <input type="url" id="company_website" name="company_website" value="{{ old('company_website', $user->company_website) }}"
                               placeholder="https://www.example.com"
                               class="shadow-sm appearance-none border @error('company_website') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('company_website') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="mt-6">
                        <label for="company_description" class="block text-sm font-medium text-gray-700 mb-1">Company Description</label>
                        <textarea id="company_description" name="company_description" rows="5"
                                  class="shadow-sm appearance-none border @error('company_description') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('company_description', $user->company_description) }}</textarea>
                        @error('company_description') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="mt-6">
                        <label for="company_logo" class="block text-sm font-medium text-gray-700 mb-1">Company Logo (Max 2MB)</label>
                        <input type="file" id="company_logo" name="company_logo"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('company_logo') border-red-500 @enderror">
                        @error('company_logo') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror

                        @if($user->company_logo)
                            <div class="mt-3">
                                <p class="text-xs text-gray-500 mb-1">Current logo:</p>
                                <img src="{{ Storage::url($user->company_logo) }}" alt="Company Logo" class="w-24 h-24 object-contain rounded-md shadow">
                                <div class="mt-2">
                                     <label for="remove_company_logo" class="inline-flex items-center text-xs">
                                         <input type="checkbox" name="remove_company_logo" id="remove_company_logo" value="1" class="form-checkbox h-4 w-4 text-red-600">
                                         <span class="ml-2 text-red-600 hover:text-red-800">Remove current logo</span>
                                     </label>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="mt-10 flex items-center justify-end space-x-4">
                    <a href="{{ route('employer.profile.show') }}" class="text-gray-600 hover:text-gray-800 font-medium py-2 px-4 rounded-md border border-gray-300 hover:bg-gray-50 transition duration-150">
                        Cancel
                    </a>
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 shadow-md transition duration-150">
                        Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
