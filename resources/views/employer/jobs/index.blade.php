@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-bold text-gray-800 mb-4 sm:mb-0">My Job Postings</h1>
        <a href="{{ route('employer.jobs.create') }}"
           class="inline-flex items-center bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-150 text-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Post New Job
        </a>
    </div>

    {{-- Session messages are handled by layouts.app --}}

    @if($jobs->isEmpty())
        <div class="text-center py-12 bg-white shadow-md rounded-lg">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
            <h3 class="mt-2 text-lg font-medium text-gray-900">No Job Postings Yet</h3>
            <p class="mt-1 text-sm text-gray-500">
                Click the "Post New Job" button to create your first listing.
            </p>
        </div>
    @else
        <div class="bg-white shadow-xl rounded-lg overflow-x-auto">
            <table class="min-w-full w-full table-auto">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr class="text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Title</th>
                        <th class="py-3 px-6 text-left">Area of Interest</th>
                        <th class="py-3 px-6 text-left">Location</th>
                        <th class="py-3 px-6 text-center">Expires On</th>
                        <th class="py-3 px-6 text-center">Applications</th> {{-- Placeholder for count --}}
                        <th class="py-3 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @foreach ($jobs as $job)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors duration-150">
                            <td class="py-4 px-6 text-left whitespace-nowrap">
                                <a href="{{ route('employer.jobs.show', $job) }}" class="font-medium text-blue-600 hover:text-blue-800 hover:underline">
                                    {{ $job->title }}
                                </a>
                            </td>
                            <td class="py-4 px-6 text-left">
                                {{ $job->areaOfInterest->name ?? 'N/A' }}
                            </td>
                            <td class="py-4 px-6 text-left">
                                {{ $job->location ?? 'N/A' }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                {{ $job->expiration_date ? \Carbon\Carbon::parse($job->expiration_date)->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <a href="{{ route('employer.applications.index', ['filter_job_id' => $job->id]) }}"
                                   class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                                    {{ $job->applications_count }}
                                </a>
                            </td>
                            <td class="py-4 px-6 text-center whitespace-nowrap">
                                <a href="{{ route('employer.jobs.show', $job) }}" class="text-green-500 hover:text-green-700 mr-3" title="View Job">
                                    <svg class="inline-block w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                <a href="{{ route('employer.jobs.edit', $job) }}" class="text-indigo-500 hover:text-indigo-700 mr-3" title="Edit Job">
                                    <svg class="inline-block w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </a>
                                <form action="{{ route('employer.jobs.destroy', $job) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this job posting: {{ addslashes($job->title) }}? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700" title="Delete Job">
                                        <svg class="inline-block w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-8">
            {{ $jobs->links() }}
        </div>
    @endif
</div>
@endsection
