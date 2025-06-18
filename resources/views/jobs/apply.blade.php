@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-10">
    <div class="max-w-3xl mx-auto bg-white shadow-xl rounded-lg overflow-hidden">
        <div class="px-6 py-8 md:p-10">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2 text-center">Apply for: {{ $job->title }}</h1>
            @if($job->company)
                <p class="text-md text-gray-600 text-center mb-8">at {{ $job->company->company_name ?? $job->company->name }}</p>
            @endif

            <form action="{{ route('jobs.application.store', $job) }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Applicant Details Section --}}
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Your Information</h2>
                    <p class="text-xs text-gray-500 mb-4">Please review and update your information below for this application. This information will be submitted with your application.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name', $applicant->name) }}" required
                                   class="shadow-sm appearance-none border @error('name') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('name') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                            <input type="email" id="email" name="email" value="{{ old('email', $applicant->email) }}" required
                                   class="shadow-sm appearance-none border @error('email') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('email') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone (Optional)</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone', $applicant->phone) }}"
                                   class="shadow-sm appearance-none border @error('phone') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('phone') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="course_completion_year" class="block text-sm font-medium text-gray-700 mb-1">Course Completion Year (Optional)</label>
                            <input type="number" id="course_completion_year" name="course_completion_year"
                                   value="{{ old('course_completion_year', $applicant->course_completion_year) }}"
                                   min="1980" max="{{ date('Y') + 7 }}"
                                   class="shadow-sm appearance-none border @error('course_completion_year') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('course_completion_year') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- CV and Message Section --}}
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Application Documents</h2>
                    <div class="mb-6">
                        <label for="cv_path" class="block text-sm font-medium text-gray-700 mb-1">Upload CV for this Application (PDF, DOC, DOCX - Max 2MB) <span class="text-red-500">*</span></label>
                        <input type="file" id="cv_path" name="cv_path" required
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 @error('cv_path') border-red-500 @enderror">
                        <p class="text-xs text-gray-500 mt-1">Your default CV is <a href="{{ $applicant->cv ? Storage::url($applicant->cv) : '#' }}" target="_blank" class="text-blue-500 hover:underline">{{ $applicant->cv ? 'viewable here' : 'not set' }}</a>. You can upload a tailored CV for this specific application.</p>
                        @error('cv_path') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message / Cover Letter (Optional)</label>
                        <textarea id="message" name="message" rows="6"
                                  class="shadow-sm appearance-none border @error('message') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Write a brief message to the employer for this application.">{{ old('message') }}</textarea>
                        @error('message') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="mt-10 flex items-center justify-end space-x-4">
                    <a href="{{ route('jobs.show', $job) }}" class="text-gray-600 hover:text-gray-800 font-medium py-2 px-4 rounded-md border border-gray-300 hover:bg-gray-50 transition duration-150">
                        Cancel
                    </a>
                    <button type="submit"
                            class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 shadow-md hover:shadow-lg transition duration-150 inline-flex items-center">
                        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        Submit Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
