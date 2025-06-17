@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto bg-white shadow-xl rounded-lg overflow-hidden">
        <div class="px-6 py-8 md:p-10">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-8 text-center">Manage Candidate Registration Window</h1>

            {{-- Global success/error messages are handled by layouts.app --}}
            {{-- Specific validation errors are shown below fields --}}

            <form action="{{ route('admin.regwindow.update') }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Date & Time Window Section --}}
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Date & Time Window</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Start Time <span class="text-red-500">*</span></label>
                            <input type="datetime-local" id="start_time" name="start_time"
                                   value="{{ old('start_time', $window->start_time ? \Carbon\Carbon::parse($window->start_time)->format('Y-m-d\TH:i') : '') }}"
                                   required class="shadow-sm appearance-none border @error('start_time') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('start_time') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">End Time <span class="text-red-500">*</span></label>
                            <input type="datetime-local" id="end_time" name="end_time"
                                   value="{{ old('end_time', $window->end_time ? \Carbon\Carbon::parse($window->end_time)->format('Y-m-d\TH:i') : '') }}"
                                   required class="shadow-sm appearance-none border @error('end_time') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('end_time') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Capacity & Access Section --}}
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Capacity & Access Control</h2>
                    <div class="mb-6">
                        <label for="max_registrations" class="block text-sm font-medium text-gray-700 mb-1">Max Registrations <span class="text-red-500">*</span></label>
                        <input type="number" id="max_registrations" name="max_registrations"
                               value="{{ old('max_registrations', $window->max_registrations) }}"
                               required min="0" class="shadow-sm appearance-none border @error('max_registrations') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               aria-describedby="max_registrations_help">
                        <p id="max_registrations_help" class="text-xs text-gray-500 mt-1">Set to 0 for unlimited registrations during the window.</p>
                        @error('max_registrations') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Window Password</label>
                            <input type="password" id="password" name="password"
                                   class="shadow-sm appearance-none border @error('password') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   aria-describedby="password_help">
                            <p id="password_help" class="text-xs text-gray-500 mt-1">Optional. Leave blank to keep current or remove password. If changed, stats below will reset.</p>
                            @error('password') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Window Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                     <div class="mt-4">
                        <label for="password_valid_duration_hours" class="block text-sm font-medium text-gray-700 mb-1">Password Valid Duration (Hours from first use) <span class="text-red-500">*</span></label>
                        <input type="number" id="password_valid_duration_hours" name="password_valid_duration_hours"
                               value="{{ old('password_valid_duration_hours', $window->password_valid_duration_hours ?? 2) }}"
                               required min="1" class="shadow-sm appearance-none border @error('password_valid_duration_hours') border-red-500 @enderror rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('password_valid_duration_hours') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Activation Section --}}
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Activation</h2>
                    <div class="flex items-center">
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $window->is_active) ? 'checked' : '' }}
                               class="form-checkbox h-5 w-5 text-blue-600 rounded focus:ring-blue-500 focus:ring-offset-0 border-gray-300 shadow-sm">
                        <label for="is_active" class="ml-3 block text-sm font-medium text-gray-800">
                            Registration Window is Active
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">If checked, students can register within this window's parameters. Activating this window will deactivate any other active window.</p>
                    @error('is_active') <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Current Statistics Section --}}
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4 border-b pb-2">Current Statistics <span class="text-xs font-normal text-gray-500">(Read-only)</span></h2>
                    <div class="bg-gray-50 p-6 rounded-lg shadow-inner space-y-3">
                        <div>
                            <span class="font-medium text-gray-600">Current Registrations:</span>
                            <span class="text-gray-800 font-semibold">{{ $window->current_registrations }} / {{ $window->max_registrations == 0 ? 'Unlimited' : $window->max_registrations }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-600">Password First Use Time:</span>
                            <span class="text-gray-800 font-semibold">{{ $window->first_use_time ? \Carbon\Carbon::parse($window->first_use_time)->format('M d, Y H:i:s') : 'Not used yet' }}</span>
                        </div>
                        @if($window->first_use_time && $window->password)
                        <p class="text-xs text-gray-500">Password window expires approx. {{ \Carbon\Carbon::parse($window->first_use_time)->addHours($window->password_valid_duration_hours ?? 2)->format('M d, Y H:i:s') }}</p>
                        @endif
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="mt-10 flex items-center justify-end">
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 shadow-md hover:shadow-lg transition duration-150 inline-flex items-center">
                        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        Update Registration Window
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
