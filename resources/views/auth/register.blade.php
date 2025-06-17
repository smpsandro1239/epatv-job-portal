@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-screen -mt-10"> {{-- Adjust margin if needed --}}
    <div class="w-full max-w-lg"> {{-- Wider card for more fields --}}
        <form method="POST" action="{{ route('register.store') }}" class="bg-white shadow-xl rounded-lg px-8 pt-6 pb-8 mb-4">
            @csrf
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Create Your Account</h2>

            {{-- Name --}}
            <div class="mb-4">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Full Name <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                           class="shadow appearance-none border @error('name') border-red-500 @enderror rounded w-full py-3 px-4 pl-10 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="John Doe">
                </div>
                @error('name') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
            </div>

            {{-- Email --}}
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email Address <span class="text-red-500">*</span></label>
                <div class="relative">
                     <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor"><path d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                    </div>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                           class="shadow appearance-none border @error('email') border-red-500 @enderror rounded w-full py-3 px-4 pl-10 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="you@example.com">
                </div>
                @error('email') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
            </div>

            {{-- Password --}}
            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password <span class="text-red-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <input type="password" name="password" id="password" required
                           class="shadow appearance-none border @error('password') border-red-500 @enderror rounded w-full py-3 px-4 pl-10 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="••••••••••••">
                </div>
                @error('password') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
            </div>

            {{-- Confirm Password --}}
            <div class="mb-6">
                <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">Confirm Password <span class="text-red-500">*</span></label>
                <div class="relative">
                     <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="shadow appearance-none border rounded w-full py-3 px-4 pl-10 text-gray-700 mb-3 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="••••••••••••">
                </div>
            </div>

            {{-- Role Selection --}}
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Register as: <span class="text-red-500">*</span></label>
                <div class="mt-2 flex flex-col space-y-2 md:flex-row md:space-y-0 md:space-x-4">
                    <label class="inline-flex items-center">
                        <input type="radio" class="form-radio text-blue-600" name="role" value="student" {{ old('role', 'student') == 'student' ? 'checked' : '' }}>
                        <span class="ml-2">Student / Candidate</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" class="form-radio text-blue-600" name="role" value="employer" {{ old('role') == 'employer' ? 'checked' : '' }}>
                        <span class="ml-2">Employer / Company</span>
                    </label>
                </div>
                 @error('role') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
            </div>

            {{-- Conditional Fields Placeholder (Actual display logic might need JS or backend field handling based on role) --}}
            {{-- For Employer --}}
            <div id="employer_fields" class="{{ old('role') == 'employer' ? '' : 'hidden' }}"> {{-- Basic conditional display --}}
                <div class="mb-4">
                    <label for="company_name" class="block text-gray-700 text-sm font-bold mb-2">Company Name <span class="text-red-500">*</span></label> {{-- Assuming company_name is required if employer --}}
                    <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}"
                           class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('company_name') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                </div>
                {{-- Add other company fields: company_city, company_website, company_description, company_logo --}}
            </div>

            {{-- For Student --}}
             <div id="student_fields" class="{{ old('role', 'student') == 'student' ? '' : 'hidden' }}">
                <div class="mb-4">
                    <label for="course_completion_year" class="block text-gray-700 text-sm font-bold mb-2">Course Completion Year (Optional)</label>
                    <input type="number" name="course_completion_year" id="course_completion_year" value="{{ old('course_completion_year') }}"
                           class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           min="1980" max="{{ date('Y') + 5 }}">
                    @error('course_completion_year') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                </div>
                 <div class="mb-4">
                    <label for="window_password" class="block text-gray-700 text-sm font-bold mb-2">Registration Window Password (if applicable)</label>
                    <input type="password" name="window_password" id="window_password"
                           class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('window_password') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                </div>
            </div>


            {{-- Submit Button --}}
            <div class="mt-8 flex items-center justify-center">
                <button type="submit"
                        class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 transition duration-150">
                    Register
                </button>
            </div>
            <p class="text-center text-gray-500 text-xs mt-6">
                Already have an account? <a href="{{ route('login') }}" class="text-blue-500 hover:text-blue-700 font-semibold">Sign in</a>.
            </p>
        </form>
        <p class="text-center text-gray-500 text-xs">
            &copy;{{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </div>
</div>

{{-- Basic JS for conditional fields based on role selection --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleRadios = document.querySelectorAll('input[name="role"]');
    const employerFields = document.getElementById('employer_fields');
    const studentFields = document.getElementById('student_fields');

    function toggleFields(selectedRole) {
        if (selectedRole === 'employer') {
            employerFields.classList.remove('hidden');
            studentFields.classList.add('hidden');
        } else { // student or candidate
            employerFields.classList.add('hidden');
            studentFields.classList.remove('hidden');
        }
    }

    // Initial state based on old input or default
    const currentRole = document.querySelector('input[name="role"]:checked').value;
    toggleFields(currentRole);

    roleRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            toggleFields(this.value);
        });
    });
});
</script>
@endsection
