@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">My Applications</h1>
    </div>

    {{-- Session messages already handled by layouts.app --}}

    @if($applications->isEmpty())
        <div class="text-center py-12 bg-white shadow-md rounded-lg">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-lg font-medium text-gray-900">No Applications Yet</h3>
            <p class="mt-1 text-sm text-gray-500">
                You have not applied to any jobs yet. Keep exploring!
            </p>
            <div class="mt-6">
                <a href="{{ route('jobs.index') }}"
                   class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-150">
                    Browse Available Jobs
                </a>
            </div>
        </div>
    @else
        <div class="bg-white shadow-xl rounded-lg overflow-x-auto">
            <table class="min-w-full w-full table-auto">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr class="text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Job Title</th>
                        <th class="py-3 px-6 text-left">Company</th>
                        <th class="py-3 px-6 text-left">Location</th>
                        <th class="py-3 px-6 text-center">Date Applied</th>
                        <th class="py-3 px-6 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @foreach ($applications as $application)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors duration-150">
                            <td class="py-4 px-6 text-left whitespace-nowrap">
                                @if($application->job && Route::has('jobs.show'))
                                    <a href="{{ route('jobs.show', $application->job->id) }}" class="font-medium text-blue-600 hover:text-blue-800 hover:underline">
                                        {{ $application->job->title ?? 'N/A (Job details removed)' }}
                                    </a>
                                @else
                                    {{ $application->job->title ?? 'N/A (Job details removed)' }}
                                @endif
                            </td>
                            <td class="py-4 px-6 text-left">
                                {{ $application->job->company->company_name ?? $application->job->company->name ?? 'N/A' }}
                            </td>
                            <td class="py-4 px-6 text-left">
                                {{ $application->job->location ?? 'N/A' }}
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
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-8">
            {{ $applications->links() }}
        </div>
    @endif
</div>
@endsection
