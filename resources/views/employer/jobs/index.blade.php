@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">My Job Postings</h1>
        <a href="{{ route('employer.jobs.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Post New Job
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if($jobs->isEmpty())
        <p>You have not posted any jobs yet.</p>
    @else
        <div class="bg-white shadow-md rounded my-6">
            <table class="min-w-max w-full table-auto">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Title</th>
                        <th class="py-3 px-6 text-left">Area of Interest</th>
                        <th class="py-3 px-6 text-left">Location</th>
                        <th class="py-3 px-6 text-center">Expires On</th>
                        <th class="py-3 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @foreach ($jobs as $job)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left whitespace-nowrap">
                                {{ $job->title }}
                            </td>
                            <td class="py-3 px-6 text-left">
                                {{ $job->areaOfInterest->name ?? 'N/A' }}
                            </td>
                            <td class="py-3 px-6 text-left">
                                {{ $job->location ?? 'N/A' }}
                            </td>
                            <td class="py-3 px-6 text-center">
                                {{ $job->expiration_date ? \Carbon\Carbon::parse($job->expiration_date)->format('Y-m-d') : 'N/A' }}
                            </td>
                            <td class="py-3 px-6 text-center">
                                <a href="{{ route('employer.jobs.edit', $job) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</a>
                                <form action="{{ route('employer.jobs.destroy', $job) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this job posting?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                                <!-- Optional: Link to a public view or a 'show' view if implemented -->
                                <!-- <a href="{{-- route('employer.jobs.show', $job) --}}" class="text-green-600 hover:text-green-900 ml-2">View</a> -->
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $jobs->links() }}
        </div>
    @endif
</div>
@endsection
