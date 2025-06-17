@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-6">Edit Job: {{ $job->title }}</h1>

    <form action="{{ route('employer.jobs.update', $job) }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @method('PUT')
        @include('employer.jobs._form', ['submitButtonText' => 'Update Job'])
    </form>
</div>
@endsection
