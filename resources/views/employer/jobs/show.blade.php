@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-10">
    <div class="bg-white shadow-2xl rounded-lg overflow-hidden">
        {{-- Job Header --}}
        <div class="bg-gradient-to-r from-gray-700 via-gray-800 to-gray-900 text-white p-8 md:p-12">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-4xl md:text-5xl font-extrabold mb-3">{{ $job->title }}</h1>
                    @if($job->company)
                        <p class="text-xl md:text-2xl text-gray-300 mb-1">
                            {{ $job->company->company_name ?? $job->company->name }}
                        </p>
                    @endif
                    @if($job->location)
                        <p class="text-gray-400 text-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 019.9-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                            {{ $job->location }}
                        </p>
                    @endif
                </div>
                <div>
                    <a href="{{ route('employer.jobs.edit', $job) }}"
                       class="inline-flex items-center bg-yellow-500 hover:bg-yellow-600 text-gray-800 font-semibold py-2 px-4 rounded-lg shadow-md transition duration-150 text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        Edit Job
                    </a>
                </div>
            </div>
        </div>

        <div class="p-8 md:p-12">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 border-b pb-3">Job Details</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-6 mb-8">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Area of Interest</h3>
                    <p class="text-lg text-gray-700">{{ $job->areaOfInterest->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Contract Type</h3>
                    <p class="text-lg text-gray-700">{{ $job->contract_type ?? 'N/A' }}</p>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Salary</h3>
                    <p class="text-lg text-gray-700">{{ $job->salary ?? 'Not specified' }}</p>
                </div>
                <div class="lg:col-span-1">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Expiration Date</h3>
                    <p class="text-lg text-red-600 font-semibold">{{ $job->expiration_date ? \Carbon\Carbon::parse($job->expiration_date)->format('M d, Y') : 'Not specified' }}</p>
                </div>
                <div class="md:col-span-2 lg:col-span-2">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Posted By (Contact Person for this job)</h3>
                    <p class="text-lg text-gray-700">{{ $job->postedBy->name ?? ($job->company->name ?? 'N/A') }}</p>
                    {{-- Displaying $job->postedBy->name which refers to the user who posted it.
                         This might be the same as $job->company->name if the company user posted it. --}}
                </div>
            </div>

            <h3 class="text-xl font-semibold text-gray-800 mt-8 mb-3">Full Job Description</h3>
            <div class="prose max-w-none text-gray-700 leading-relaxed bg-gray-50 p-6 rounded-md shadow-inner">
                {!! nl2br(e($job->description)) !!}
            </div>

             <div class="mt-10 pt-6 border-t">
                <a href="{{ route('employer.applications.index', ['job_id' => $job->id]) }}" {{-- Assuming filter by job_id --}}
                   class="bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-3 px-6 rounded-lg text-lg shadow-md hover:shadow-lg transition duration-150 mr-4">
                    View Applications for this Job ({{ $job->applications_count ?? 0 }}) {{-- Add withCount('applications') in controller --}}
                </a>
            </div>

            <div class="mt-10 text-center">
                <a href="{{ route('employer.jobs.index') }}" class="text-blue-600 hover:underline">&laquo; Back to My Job Postings</a>
            </div>
        </div>
    </div>
</div>
@endsection
