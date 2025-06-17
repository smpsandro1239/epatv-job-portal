@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-6">Job Applications Received</h1>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if($applications->isEmpty())
        <p>No applications received for your job postings yet.</p>
    @else
        <div class="bg-white shadow-md rounded my-6">
            <table class="min-w-max w-full table-auto">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Applicant Name</th>
                        <th class="py-3 px-6 text-left">Applicant Email</th>
                        <th class="py-3 px-6 text-left">CV</th>
                        <th class="py-3 px-6 text-left">Job Title Applied For</th>
                        <th class="py-3 px-6 text-center">Date Applied</th>
                        <th class="py-3 px-6 text-center">Status</th>
                        <!-- Add actions column if status update is implemented -->
                        <!-- <th class="py-3 px-6 text-center">Actions</th> -->
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @foreach ($applications as $application)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left whitespace-nowrap">
                                {{ $application->user->name ?? 'N/A' }}
                            </td>
                            <td class="py-3 px-6 text-left">
                                {{ $application->user->email ?? 'N/A' }}
                            </td>
                            <td class="py-3 px-6 text-left">
                                @if($application->user && $application->user->cv)
                                    <a href="{{ Storage::url($application->user->cv) }}" target="_blank" class="text-blue-500 hover:underline">View CV</a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="py-3 px-6 text-left">
                                {{ $application->job->title ?? 'N/A (Job details removed)' }}
                            </td>
                            <td class="py-3 px-6 text-center">
                                {{ $application->created_at->format('Y-m-d') }}
                            </td>
                            <td class="py-3 px-6 text-center">
                                <span class="bg-{{ strtolower(str_replace(' ', '-', $application->status ?? '')) }}-200 text-{{ strtolower(str_replace(' ', '-', $application->status ?? '')) }}-600 py-1 px-3 rounded-full text-xs">
                                    {{ ucfirst($application->status ?? 'Unknown') }}
                                </span>
                            </td>
                            <!-- Example for status update - for future enhancement
                            <td class="py-3 px-6 text-center">
                                <select onchange="updateStatus(this, {{ $application->id }})">
                                    <option value="pending" {{ $application->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="reviewed" {{ $application->status == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                                    <option value="shortlisted" {{ $application->status == 'shortlisted' ? 'selected' : '' }}>Shortlisted</option>
                                    <option value="rejected" {{ $application->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </td>
                            -->
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $applications->links() }}
        </div>
    @endif
</div>
@endsection
