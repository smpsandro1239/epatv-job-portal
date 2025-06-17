@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-xl rounded-lg overflow-hidden">
        <div class="px-6 py-8 md:p-10">
            <div class="flex flex-wrap md:flex-nowrap items-start">
                <!-- Left Column: Photo and CV -->
                <div class="w-full md:w-1/3 md:pr-8 mb-6 md:mb-0">
                    @if($user->photo)
                        <div class="mb-6 text-center">
                            <img src="{{ Storage::url($user->photo) }}" alt="{{ $user->name }}'s Photo"
                                 class="w-48 h-48 object-cover rounded-full mx-auto shadow-md border-4 border-gray-200">
                        </div>
                    @else
                        <div class="mb-6 text-center">
                            {{-- Placeholder for photo --}}
                            <div class="w-48 h-48 bg-gray-200 rounded-full mx-auto flex items-center justify-center text-gray-400 shadow-md border-4 border-gray-200">
                                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            <p class="text-sm text-gray-500 mt-2">No photo uploaded.</p>
                        </div>
                    @endif

                    @if($user->cv)
                        <div class="text-center mb-6">
                            <a href="{{ Storage::url($user->cv) }}" target="_blank"
                               class="inline-block bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-150">
                                <svg class="inline-block w-5 h-5 mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Download CV
                            </a>
                        </div>
                    @else
                        <div class="text-center mb-6">
                            <p class="text-gray-500 italic">No CV uploaded.</p>
                        </div>
                    @endif
                     <div class="text-center">
                        <a href="{{ route('student.profile.edit') }}"
                           class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-150">
                           <svg class="inline-block w-5 h-5 mr-2 -mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            Edit Profile
                        </a>
                    </div>
                </div>

                <!-- Right Column: Details -->
                <div class="w-full md:w-2/3">
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-1">{{ $user->name }}</h1>
                    <p class="text-gray-600 text-lg mb-6">{{ $user->email }}</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Phone</label>
                            <p class="text-lg text-gray-800">{{ $user->phone ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Course Completion Year</label>
                            <p class="text-lg text-gray-800">{{ $user->course_completion_year ?? 'Not provided' }}</p>
                        </div>
                         <div>
                            <label class="block text-sm font-medium text-gray-500">Profile Status</label>
                            <p class="text-lg text-gray-800 capitalize">{{ $user->registration_status ?? 'N/A' }}</p>
                        </div>
                         <div>
                            <label class="block text-sm font-medium text-gray-500">Email Verified</label>
                            <p class="text-lg {{ $user->email_verified_at ? 'text-green-600' : 'text-red-600' }}">
                                {{ $user->email_verified_at ? 'Yes (' . $user->email_verified_at->format('M d, Y') . ')' : 'No' }}
                            </p>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-xl font-semibold text-gray-700 mb-3">Areas of Interest</h3>
                        @if($user->areasOfInterest->isNotEmpty())
                            <div class="flex flex-wrap gap-2">
                                @foreach($user->areasOfInterest as $area)
                                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-medium shadow-sm">
                                        {{ $area->name }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-600 italic">No areas of interest specified.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
