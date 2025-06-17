@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-bold text-gray-800 mb-4 sm:mb-0">Job Applications Received</h1>
        {{-- Add any primary action like "Export" if needed in future --}}
    </div>

    {{-- Filter Form - Placeholder: Backend logic for filters needs to be implemented in EmployerController@listApplications --}}
    <form method="GET" action="{{ route('employer.applications.index') }}" class="mb-8 p-6 bg-white shadow-lg rounded-lg border border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
            <div>
                <label for="filter_job_id" class="block text-sm font-medium text-gray-700 mb-1">Filter by Job</label>
                <select name="filter_job_id" id="filter_job_id"
                        class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Jobs</option>
                    @if(isset($employerJobs))
                        @foreach($employerJobs as $job)
                            <option value="{{ $job->id }}" {{ (isset($filters['filter_job_id']) && $filters['filter_job_id'] == $job->id) ? 'selected' : '' }}>
                                {{ $job->title }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div>
                <label for="filter_status" class="block text-sm font-medium text-gray-700 mb-1">Filter by Status</label>
                <select name="filter_status" id="filter_status"
                        class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Statuses</option>
                    @if(isset($statuses))
                        @foreach($statuses as $statusValue)
                            <option value="{{ $statusValue }}" {{ (isset($filters['filter_status']) && $filters['filter_status'] == $statusValue) ? 'selected' : '' }}>
                                {{ ucfirst($statusValue) }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="flex space-x-3">
                 <button type="submit"
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-75 transition duration-150">
                    Filter Applications
                </button>
                 <a href="{{ route('employer.applications.index') }}"
                   class="w-full text-center bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-75 transition duration-150">
                    Clear
                </a>
            </div>
        </div>
        {{-- Removed note about controller logic as it's now being implemented --}}
    </form>


    @if($applications->isEmpty())
        <div class="text-center py-12 bg-white shadow-md rounded-lg">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"></path></svg>
            <h3 class="mt-2 text-lg font-medium text-gray-900">No Applications Found</h3>
            <p class="mt-1 text-sm text-gray-500">
                No applications match your current filters, or no applications have been received yet.
            </p>
        </div>
    @else
        <div class="bg-white shadow-xl rounded-lg overflow-x-auto">
            <table class="min-w-full w-full table-auto">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr class="text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Applicant</th>
                        <th class="py-3 px-6 text-left">Contact</th> {{-- Combined Email & Phone --}}
                        <th class="py-3 px-6 text-left">CV</th>
                        <th class="py-3 px-6 text-left">Job Title Applied For</th>
                        <th class="py-3 px-6 text-center">Date Applied</th>
                        <th class="py-3 px-6 text-center">Status</th>
                        {{-- <th class="py-3 px-6 text-center">Actions</th> --}}
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @foreach ($applications as $application)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors duration-150">
                            <td class="py-4 px-6 text-left">
                                <div class="font-medium">{{ $application->user->name ?? 'N/A' }}</div>
                                {{-- Optionally, add student's Area of Interest or other quick info if needed --}}
                            </td>
                            <td class="py-4 px-6 text-left">
                                @if($application->user)
                                    <div class="text-xs">{{ $application->user->email ?? 'No Email' }}</div>
                                    <div class="text-xs text-gray-500 mt-1">{{ $application->user->phone ?? 'No Phone' }}</div>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="py-4 px-6 text-left">
                                @if($application->user && $application->user->cv)
                                    <a href="{{ Storage::url($application->user->cv) }}" target="_blank"
                                       class="inline-flex items-center text-blue-500 hover:text-blue-700 hover:underline text-xs font-semibold py-1 px-2 bg-blue-50 hover:bg-blue-100 rounded-md">
                                       <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        View CV
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400 italic">No CV</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-left">
                                @if($application->job)
                                    <a href="{{ route('employer.jobs.show', $application->job->id) }}" class="font-medium text-blue-600 hover:text-blue-800 hover:underline">
                                        {{ $application->job->title ?? 'N/A (Job details removed)' }}
                                    </a>
                                @else
                                    {{ 'N/A (Job details removed)' }}
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                {{ $application->created_at->format('M d, Y') }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                @php
                                    $status = strtolower($application->status ?? 'unknown');
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'reviewed' => 'bg-blue-100 text-blue-700',
                                        'shortlisted' => 'bg-purple-100 text-purple-700',
                                        'hired' => 'bg-green-100 text-green-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                        'unknown' => 'bg-gray-100 text-gray-700',
                                    ];
                                    $bgColor = $statusColors[$status] ?? $statusColors['unknown'];
                                @endphp
                                <span class="{{ $bgColor }} py-1 px-3 rounded-full text-xs font-semibold">
                                    {{ ucfirst($application->status ?? 'Unknown') }}
                                </span>
                            </td>
                            {{-- Placeholder for actions like "Update Status" --}}
                            {{-- <td class="py-4 px-6 text-center"> ... </td> --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-8">
            {{ $applications->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
