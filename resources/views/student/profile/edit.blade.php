@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white shadow-xl rounded-lg overflow-hidden">
        <div class="px-6 py-8 md:p-10">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-8 text-center">Edit Your Profile</h1>

            {{-- Error messages are now handled globally by layouts.app, but can keep a specific one if desired --}}
            {{-- @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Please correct the errors below:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif --}}

            <form action="{{ route('student.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Personal Details Section --}}
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Personal Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                   class="shadow-sm appearance-none border @error('name') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('name') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                   class="shadow-sm appearance-none border @error('phone') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('phone') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="mt-6">
                        <label for="course_completion_year" class="block text-sm font-medium text-gray-700 mb-1">Course Completion Year</label>
                        <input type="number" id="course_completion_year" name="course_completion_year"
                               value="{{ old('course_completion_year', $user->course_completion_year) }}"
                               min="1980" max="{{ date('Y') + 7 }}"
                               class="shadow-sm appearance-none border @error('course_completion_year') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('course_completion_year') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- File Uploads Section --}}
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Profile Files</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="photo" class="block text-sm font-medium text-gray-700 mb-1">Profile Photo (Max 2MB)</label>
                            <input type="file" id="photo" name="photo" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('photo') border-red-500 @enderror">
                            @error('photo') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                            @if($user->photo)
                                <div class="mt-3">
                                    <p class="text-xs text-gray-500 mb-1">Current photo:</p>
                                    <img src="{{ Storage::url($user->photo) }}" alt="Current Photo" class="w-24 h-24 object-cover rounded-md shadow">
                                </div>
                            @endif
                        </div>
                        <div>
                            <label for="cv" class="block text-sm font-medium text-gray-700 mb-1">CV (PDF, DOC, DOCX - Max 2MB)</label>
                            <input type="file" id="cv" name="cv" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 @error('cv') border-red-500 @enderror">
                            @error('cv') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                            @if($user->cv)
                                <div class="mt-3">
                                    <p class="text-xs text-gray-500 mb-1">Current CV: <a href="{{ Storage::url($user->cv) }}" target="_blank" class="text-blue-500 hover:underline">Download</a></p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Areas of Interest Section --}}
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Areas of Interest</h2>
                    <div>
                        <label for="areas_of_interest" class="block text-sm font-medium text-gray-700 mb-1">Select your areas of interest</label>
                        <select name="areas_of_interest[]" id="areas_of_interest" multiple
                                class="shadow-sm block w-full mt-1 border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('areas_of_interest') border-red-500 @enderror"
                                size="5"> {{-- `size` attribute makes it look more like a multi-select box --}}
                            @foreach($allAreasOfInterest as $area)
                                <option value="{{ $area->id }}"
                                    {{ (in_array($area->id, old('areas_of_interest', $user->areasOfInterest->pluck('id')->toArray())) ? 'selected' : '') }}>
                                    {{ $area->name }}
                                </option>
                            @endforeach
                        </select>
                         <p class="text-xs text-gray-500 mt-1">Hold down Ctrl (Windows) or Command (Mac) to select multiple options.</p>
                        @error('areas_of_interest') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                        @error('areas_of_interest.*') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="mt-10 flex items-center justify-end space-x-4">
                    <a href="{{ route('student.profile.show') }}" class="text-gray-600 hover:text-gray-800 font-medium py-2 px-4 rounded-md border border-gray-300 hover:bg-gray-50 transition duration-150">
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
