@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-6">Manage Candidate Registration Window</h1>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Validation Errors!</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.regwindow.update') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="start_time" class="block text-gray-700 text-sm font-bold mb-2">Start Time:</label>
                <input type="datetime-local" id="start_time" name="start_time"
                       value="{{ old('start_time', $window->start_time ? \Carbon\Carbon::parse($window->start_time)->format('Y-m-d\TH:i') : '') }}"
                       required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('start_time') border-red-500 @enderror">
                @error('start_time') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="end_time" class="block text-gray-700 text-sm font-bold mb-2">End Time:</label>
                <input type="datetime-local" id="end_time" name="end_time"
                       value="{{ old('end_time', $window->end_time ? \Carbon\Carbon::parse($window->end_time)->format('Y-m-d\TH:i') : '') }}"
                       required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('end_time') border-red-500 @enderror">
                @error('end_time') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-4">
            <label for="max_registrations" class="block text-gray-700 text-sm font-bold mb-2">Max Registrations (0 for unlimited):</label>
            <input type="number" id="max_registrations" name="max_registrations"
                   value="{{ old('max_registrations', $window->max_registrations) }}"
                   required min="0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('max_registrations') border-red-500 @enderror">
            @error('max_registrations') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
        </div>

        <div class="mt-4">
            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Window Password (optional - leave blank to keep current or remove if empty):</label>
            <input type="password" id="password" name="password"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('password') border-red-500 @enderror"
                   aria-describedby="passwordHelp">
            <p id="passwordHelp" class="text-xs text-gray-600 mt-1">If you change this, current_registrations and first_use_time will be reset.</p>
            @error('password') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
        </div>
        <div class="mt-4">
            <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">Confirm Window Password:</label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>


        <div class="mt-6">
            <label class="flex items-center">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $window->is_active) ? 'checked' : '' }} class="form-checkbox h-5 w-5 text-blue-600">
                <span class="ml-2 text-gray-700 text-sm font-bold">Is Active</span>
            </label>
            @error('is_active') <p class="text-red-500 text-xs italic">{{ $message }}</p> @enderror
        </div>

        <div class="mt-6 p-4 border rounded bg-gray-50">
            <h3 class="text-lg font-semibold mb-2">Current Stats (Read-only)</h3>
            <p><strong>Current Registrations:</strong> {{ $window->current_registrations }}</p>
            <p><strong>First Use Time:</strong> {{ $window->first_use_time ? \Carbon\Carbon::parse($window->first_use_time)->format('Y-m-d H:i:s') : 'Not used yet' }}</p>
        </div>

        <div class="mt-8">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Update Registration Window
            </button>
        </div>
    </form>
</div>
@endsection
