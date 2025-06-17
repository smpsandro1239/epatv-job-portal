@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-6">Create New Job</h1>

    <form action="{{ route('employer.jobs.store') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @include('employer.jobs._form', ['submitButtonText' => 'Create Job'])
    </form>
</div>
@endsection
