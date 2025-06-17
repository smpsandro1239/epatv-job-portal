@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">{{ $job->title }}</h1>
            <a href="{{ route('employer.jobs.edit', $job) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Edit This Job
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
            <div>
                <strong class="block text-gray-700 text-sm font-bold mb-1">Area of Interest:</strong>
                <p class="text-gray-700">{{ $job->areaOfInterest->name ?? 'N/A' }}</p>
            </div>
            <div>
                <strong class="block text-gray-700 text-sm font-bold mb-1">Location:</strong>
                <p class="text-gray-700">{{ $job->location ?? 'N/A' }}</p>
            </div>
            <div>
                <strong class="block text-gray-700 text-sm font-bold mb-1">Contract Type:</strong>
                <p class="text-gray-700">{{ $job->contract_type ?? 'N/A' }}</p>
            </div>
            <div>
                <strong class="block text-gray-700 text-sm font-bold mb-1">Salary:</strong>
                <p class="text-gray-700">{{ $job->salary ?? 'N/A' }}</p>
            </div>
            <div>
                <strong class="block text-gray-700 text-sm font-bold mb-1">Expiration Date:</strong>
                <p class="text-gray-700">{{ $job->expiration_date ? \Carbon\Carbon::parse($job->expiration_date)->format('M d, Y') : 'N/A' }}</p>
            </div>
             <div>
                <strong class="block text-gray-700 text-sm font-bold mb-1">Posted By (Company Contact):</strong>
                <p class="text-gray-700">{{ $job->company->name ?? 'N/A' }}</p> {{-- Assuming company relationship on Job model points to User model --}}
            </div>
        </div>

        <div class="mt-6">
            <strong class="block text-gray-700 text-sm font-bold mb-1">Description:</strong>
            <div class="prose max-w-full">{!! nl2br(e($job->description)) !!}</div>
        </div>

        <div class="mt-8">
            <a href="{{ route('employer.jobs.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                &laquo; Back to My Job Postings
            </a>
        </div>
    </div>
</div>
@endsection
