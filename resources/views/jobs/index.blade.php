@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-4xl font-extrabold text-gray-800 mb-10 text-center">Discover Your Next Opportunity</h1>

    <!-- Filter Form -->
    <form action="{{ route('jobs.index') }}" method="GET" class="mb-10 p-6 bg-white shadow-lg rounded-lg border border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 items-end">
            <div>
                <label for="area_of_interest_id" class="block text-sm font-medium text-gray-700 mb-1">Area of Interest</label>
                <select name="area_of_interest_id" id="area_of_interest_id"
                        class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Areas</option>
                    @foreach($areasOfInterest as $area)
                        <option value="{{ $area->id }}" {{ (isset($filters['area_of_interest_id']) && $filters['area_of_interest_id'] == $area->id) ? 'selected' : '' }}>
                            {{ $area->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                <select name="location" id="location"
                        class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Locations</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc }}" {{ (isset($filters['location']) && $filters['location'] == $loc) ? 'selected' : '' }}>
                            {{ $loc }}
                        </option>
                    @endforeach
                </select>
                {{-- Alternative: Text input for location search
                <input type="text" name="location" id="location" value="{{ $filters['location'] ?? '' }}" placeholder="e.g., City, Remote"
                       class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                --}}
            </div>
            <div>
                <label for="contract_type" class="block text-sm font-medium text-gray-700 mb-1">Contract Type</label>
                <select name="contract_type" id="contract_type"
                        class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Types</option>
                    @foreach($contractTypes as $type)
                        <option value="{{ $type }}" {{ (isset($filters['contract_type']) && $filters['contract_type'] == $type) ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex space-x-3">
                <button type="submit"
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-75 transition duration-150">
                    Filter Jobs
                </button>
                <a href="{{ route('jobs.index') }}"
                   class="w-full text-center bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-75 transition duration-150">
                    Clear
                </a>
            </div>
        </div>
    </form>

    @if($jobs->isEmpty())
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
            </svg>
            <h3 class="mt-2 text-lg font-medium text-gray-900">No jobs found</h3>
            <p class="mt-1 text-sm text-gray-500">
                There are no jobs matching your current criteria. Try broadening your search!
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach ($jobs as $job)
                <div class="bg-white shadow-lg rounded-lg overflow-hidden hover:shadow-xl transition-shadow duration-300 flex flex-col">
                    <div class="p-6 flex-grow">
                        <h3 class="text-2xl font-semibold text-gray-800 mb-2">
                            <a href="{{ route('jobs.show', $job->id) }}" class="hover:text-blue-600 hover:underline">
                                {{ $job->title }}
                            </a>
                        </h3>

                        <div class="mb-3">
                            @if($job->company)
                                <p class="text-md text-gray-700 font-medium">{{ $job->company->company_name ?? $job->company->name }}</p>
                            @else
                                <p class="text-md text-gray-600 italic">Company not specified</p>
                            @endif
                            @if($job->location)
                                <p class="text-sm text-gray-500 flex items-center mt-1">
                                    <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 019.9-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
                                    {{ $job->location }}
                                </p>
                            @endif
                        </div>

                        <div class="mb-3">
                            <span class="inline-block bg-blue-100 text-blue-700 text-xs font-semibold px-2.5 py-0.5 rounded-full mr-2">
                                {{ $job->areaOfInterest->name ?? 'N/A' }}
                            </span>
                            @if($job->contract_type)
                            <span class="inline-block bg-green-100 text-green-700 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                                {{ $job->contract_type }}
                            </span>
                            @endif
                        </div>

                        <p class="text-gray-600 text-sm mt-1 mb-4 leading-relaxed">{{ Str::limit($job->description, 120) }}</p>

                        @if($job->salary)
                            <p class="text-sm text-gray-500 mb-1"><span class="font-semibold">Salary:</span> {{ $job->salary }}</p>
                        @endif
                        @if($job->expiration_date)
                            <p class="text-xs text-red-500">Expires on: {{ \Carbon\Carbon::parse($job->expiration_date)->format('M d, Y') }}</p>
                        @endif
                    </div>

                    <div class="p-6 bg-gray-50 border-t border-gray-200">
                        {{-- Assuming a job details route might exist in future, e.g., jobs.show --}}
                        {{-- For now, direct apply or save might be more relevant if no public show page --}}
                         @if(Route::has('jobs.show')) {{-- A hypothetical public job show route --}}
                            <a href="{{ route('jobs.show', $job->id) }}"
                               class="block w-full text-center bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-150">
                                View Details
                            </a>
                        @else
                             <p class="text-xs text-gray-400 text-center">Details link placeholder</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-12">
            {{ $jobs->appends(request()->query())->links() }} {{-- Tailwind pagination views should be configured for this to look good --}}
        </div>
    @endif
</div>
@endsection
