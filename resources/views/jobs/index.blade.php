@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-6">Job Listings</h2>

    <!-- Filter Form -->
    <form action="{{ route('jobs.index') }}" method="GET" class="mb-6 p-4 border rounded">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="area_of_interest_id" class="block text-sm font-medium text-gray-700">Area of Interest</label>
                <select name="area_of_interest_id" id="area_of_interest_id" class="mt-1 block w-full p-2 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">All Areas</option>
                    @foreach($areasOfInterest as $area)
                        <option value="{{ $area->id }}" {{ (isset($filters['area_of_interest_id']) && $filters['area_of_interest_id'] == $area->id) ? 'selected' : '' }}>
                            {{ $area->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                <select name="location" id="location" class="mt-1 block w-full p-2 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">All Locations</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc }}" {{ (isset($filters['location']) && $filters['location'] == $loc) ? 'selected' : '' }}>
                            {{ $loc }}
                        </option>
                    @endforeach
                </select>
                <!-- Or use a text input for location search: -->
                <!-- <input type="text" name="location" id="location" value="{{ $filters['location'] ?? '' }}" class="mt-1 block w-full p-2 border-gray-300 rounded-md shadow-sm"> -->
            </div>

            <div>
                <label for="contract_type" class="block text-sm font-medium text-gray-700">Contract Type</label>
                <select name="contract_type" id="contract_type" class="mt-1 block w-full p-2 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">All Types</option>
                    @foreach($contractTypes as $type)
                        <option value="{{ $type }}" {{ (isset($filters['contract_type']) && $filters['contract_type'] == $type) ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Filter</button>
            <a href="{{ route('jobs.index') }}" class="ml-2 px-4 py-2 bg-gray-300 text-black rounded hover:bg-gray-400">Clear Filters</a>
        </div>
    </form>

    @if($jobs->isEmpty())
        <p>No jobs found matching your criteria.</p>
    @else
        @foreach ($jobs as $job)
            <div class="border p-4 mb-4 rounded shadow">
                <h3 class="text-xl font-semibold">{{ $job->title }}</h3>
                <p class="text-gray-600">
                    @if($job->company)
                        Company: {{ $job->company->name }}
                    @else
                        Company: Not specified
                    @endif
                    @if($job->location)
                        | Location: {{ $job->location }}
                    @endif
                </p>
                <p class="text-sm text-gray-500">Area: {{ $job->areaOfInterest->name ?? 'N/A' }}</p>
                <p class="mt-2">{{ Str::limit($job->description, 150) }}</p>
                @if($job->contract_type)
                    <p class="mt-1"><strong>Contract Type:</strong> {{ $job->contract_type }}</p>
                @endif
                @if($job->salary)
                    <p><strong>Salary:</strong> {{ $job->salary }}</p>
                @endif
                @if($job->expiration_date)
                    <p class="text-sm text-gray-500">Expires on: {{ \Carbon\Carbon::parse($job->expiration_date)->format('M d, Y') }}</p>
                @endif
                <!-- Add a link to job details page if you have one -->
                <!-- <a href="{{-- route('jobs.show', $job->id) --}}" class="text-blue-500 hover:underline">View Details</a> -->
            </div>
        @endforeach

        <div class="mt-6">
            {{ $jobs->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
