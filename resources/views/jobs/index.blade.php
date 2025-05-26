@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-bold mb-6">Job Listings</h2>
        @foreach ($jobs as $job)
            <div class="border p-4 mb-4 rounded">
                <h3 class="text-xl font-semibold">{{ $job->title }}</h3>
                <p>{{ $job->description }}</p>
                <p><strong>Area:</strong> {{ $job->areaOfInterest->name }}</p>
                <p><strong>Status:</strong> {{ $job->status }}</p>
            </div>
        @endforeach
    </div>
@endsection
