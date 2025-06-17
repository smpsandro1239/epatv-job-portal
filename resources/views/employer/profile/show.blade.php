@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
        <div class="px-6 py-8 md:p-10">
            <div class="flex justify-between items-start mb-6 pb-4 border-b border-gray-200">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800">Company Profile</h1>
                <a href="{{ route('employer.profile.edit') }}"
                   class="inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-150 text-sm">
                   <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    Edit Profile
                </a>
            </div>

            {{-- Global session messages are handled by layouts.app --}}

            <div class="flex flex-wrap md:flex-nowrap items-start">
                <!-- Left Column: Company Logo -->
                <div class="w-full md:w-1/3 md:pr-8 mb-8 md:mb-0 text-center md:text-left">
                    @if($user->company_logo)
                        <img src="{{ Storage::url($user->company_logo) }}" alt="{{ $user->company_name ?? $user->name }} Logo"
                             class="w-48 h-48 object-contain rounded-lg shadow-md border-2 border-gray-200 md:mx-0 mx-auto">
                    @else
                        <div class="w-48 h-48 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 border-2 border-dashed border-gray-300 md:mx-0 mx-auto">
                            <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <p class="text-sm text-gray-500 mt-2 text-center md:text-left">No company logo uploaded.</p>
                    @endif
                </div>

                <!-- Right Column: Details -->
                <div class="w-full md:w-2/3">
                    <div class="mb-6 pb-6 border-b border-gray-200">
                        <h2 class="text-xl font-semibold text-gray-700 mb-3">Contact Information</h2>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Contact Person</dt>
                                <dd class="mt-1 text-md text-gray-900">{{ $user->name }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Contact Email</dt>
                                <dd class="mt-1 text-md text-gray-900">{{ $user->email }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h2 class="text-xl font-semibold text-gray-700 mb-3">Company Details</h2>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3">
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Company Name</dt>
                                <dd class="mt-1 text-md text-gray-900">{{ $user->company_name ?? 'Not provided' }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Company City</dt>
                                <dd class="mt-1 text-md text-gray-900">{{ $user->company_city ?? 'Not provided' }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">Company Website</dt>
                                @if($user->company_website)
                                    <dd class="mt-1 text-md">
                                        <a href="{{ Str::startsWith($user->company_website, ['http://', 'https://']) ? $user->company_website : '//'.$user->company_website }}"
                                           target="_blank" rel="noopener noreferrer"
                                           class="text-blue-600 hover:text-blue-800 hover:underline">
                                            {{ $user->company_website }}
                                        </a>
                                    </dd>
                                @else
                                    <dd class="mt-1 text-md text-gray-600 italic">Not provided</dd>
                                @endif
                            </div>
                            <div class="sm:col-span-2 mt-2">
                                <dt class="text-sm font-medium text-gray-500">Company Description</dt>
                                <dd class="mt-1 text-md text-gray-700 prose max-w-none">
                                    {!! nl2br(e($user->company_description ?? 'No description provided.')) !!}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
