@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-6">My Applications</h1>

    @if($applications->isEmpty())
        <p>You have not applied to any jobs yet.</p>
        <p><a href="{{ route('jobs.index') }}" class="text-blue-500 hover:underline">Browse available jobs</a></p>
    @else
        <div class="bg-white shadow-md rounded my-6">
            <table class="min-w-max w-full table-auto">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Job Title</th>
                        <th class="py-3 px-6 text-left">Company</th>
                        <th class="py-3 px-6 text-left">Location</th>
                        <th class="py-3 px-6 text-center">Date Applied</th>
                        <th class="py-3 px-6 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @foreach ($applications as $application)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left whitespace-nowrap">
                                {{ $application->job->title ?? 'N/A (Job details removed)' }}
                            </td>
                            <td class="py-3 px-6 text-left">
                                {{ $application->job->company->name ?? 'N/A' }}
                            </td>
                            <td class="py-3 px-6 text-left">
                                {{ $application->job->location ?? 'N/A' }}
                            </td>
                            <td class="py-3 px-6 text-center">
                                {{ $application->created_at->format('Y-m-d') }}
                            </td>
                            <td class="py-3 px-6 text-center">
                                <span class="bg-{{ strtolower(str_replace(' ', '-', $application->status ?? '')) }}-200 text-{{ strtolower(str_replace(' ', '-', $application->status ?? '')) }}-600 py-1 px-3 rounded-full text-xs">
                                    {{ ucfirst($application->status ?? 'Unknown') }}
                                </span>
                            </td>
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
