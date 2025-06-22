@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-100 to-sky-100 py-6 flex flex-col justify-center sm:py-12">
    <div class="relative py-3 sm:max-w-xl sm:mx-auto w-full"> {{-- Adjusted for centering and responsiveness --}}
        <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-sky-500 shadow-lg transform -skew-y-6 sm:skew-y-0 sm:-rotate-6 sm:rounded-3xl"></div> {{-- Decorative background --}}
        <div class="relative bg-white shadow-2xl rounded-xl p-10 sm:p-12 w-full max-w-xl">
            <form method="POST" action="{{ route('register.store') }}" enctype="multipart/form-data">
                @csrf
                {{-- Hidden role input, value set by controller via $type --}}
                <input type="hidden" name="role" value="{{ ($type === 'company') ? 'employer' : ($type ?? old('role', 'student')) }}">

                <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Create Your Account as {{ ucfirst($type ?? 'User') }}</h2>

                {{-- Name --}}
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Full Name <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                               class="border @error('name') border-red-500 @enderror rounded w-full py-3 px-4 pl-10 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:ring-offset-2 disabled:opacity-50"
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
                               class="border @error('email') border-red-500 @enderror rounded w-full py-3 px-4 pl-10 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:ring-offset-2 disabled:opacity-50"
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
                               class="border @error('password') border-red-500 @enderror rounded w-full py-3 px-4 pl-10 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:ring-offset-2 disabled:opacity-50"
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
                               class="border rounded w-full py-3 px-4 pl-10 text-gray-700 mb-3 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:ring-offset-2 disabled:opacity-50"
                               placeholder="••••••••••••">
                    </div>
                </div>

                {{-- Conditional Fields --}}
                {{-- For Employer --}}
                <div id="employer_fields" class="hidden">
                    <div class="mb-4">
                        <label for="company_name" class="block text-gray-700 text-sm font-bold mb-2">Company Name <span class="text-red-500">*</span></label>
                        <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}"
                               class="border @error('company_name') border-red-500 @enderror rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:ring-offset-2 disabled:opacity-50" placeholder="Your Company LLC">
                        @error('company_name') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="company_city" class="block text-gray-700 text-sm font-bold mb-2">Cidade <span class="text-red-500">*</span></label>
                        <input type="text" name="company_city" id="company_city" value="{{ old('company_city') }}"
                               class="border @error('company_city') border-red-500 @enderror rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:ring-offset-2 disabled:opacity-50" placeholder="Ex: Braga">
                        @error('company_city') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="company_website" class="block text-gray-700 text-sm font-bold mb-2">Site da empresa (opcional)</label>
                        <input type="url" name="company_website" id="company_website" value="{{ old('company_website') }}"
                               class="border @error('company_website') border-red-500 @enderror rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:ring-offset-2 disabled:opacity-50" placeholder="https://www.asuaempresa.com">
                        @error('company_website') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4"> {{-- Employer Phone --}}
                        <label for="employer_phone" class="block text-gray-700 text-sm font-bold mb-2">Telefone (Contacto) <span class="text-red-500">*</span></label>
                        <input type="tel" name="employer_phone_disabled" id="employer_phone" value="{{ old('phone') }}"
                               class="border @error('phone') border-red-500 @enderror rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:ring-offset-2 disabled:opacity-50" placeholder="Ex: 912345678">
                        @error('phone') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="company_description" class="block text-gray-700 text-sm font-bold mb-2">Descrição da empresa</label>
                        <textarea name="company_description" id="company_description" rows="4"
                                  class="border @error('company_description') border-red-500 @enderror rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:ring-offset-2 disabled:opacity-50"
                                  placeholder="Fale um pouco sobre a sua empresa...">{{ old('company_description') }}</textarea>
                        @error('company_description') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="company_logo" class="block text-gray-700 text-sm font-bold mb-2">Logótipo da empresa (opcional)</label>
                        <input type="file" name="company_logo" id="company_logo"
                               class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border file:border-gray-300 file:text-sm file:font-medium file:bg-slate-50 file:text-slate-700 hover:file:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:ring-offset-2 @error('company_logo') border-red-500 @enderror">
                        <p class="text-xs text-gray-500 mt-1">Maximum file size: 2 MB.</p>
                        @error('company_logo') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- For Student --}}
                 <div id="student_fields" class="hidden">
                    <div class="mb-4">
                        <label for="course_completion_year" class="block text-gray-700 text-sm font-bold mb-2">Course Completion Year (Optional)</label>
                        <input type="number" name="course_completion_year" id="course_completion_year" value="{{ old('course_completion_year') }}"
                               class="border @error('course_completion_year') border-red-500 @enderror rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:ring-offset-2 disabled:opacity-50"
                               min="1980" max="{{ date('Y') + 7 }}" placeholder="E.g., {{ date('Y') }}">
                        @error('course_completion_year') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                    </div>
                    <div class="mb-4"> {{-- Student Phone --}}
                        <label for="student_phone" class="block text-gray-700 text-sm font-bold mb-2">Telefone (Opcional)</label>
                        <input type="tel" name="student_phone_disabled" id="student_phone" value="{{ old('phone') }}"
                               class="border @error('phone') border-red-500 @enderror rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:ring-offset-2 disabled:opacity-50"
                               placeholder="Ex: 912345678">
                        @error('phone') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                    </div>
                     <div class="mb-4">
                        <label for="window_password" class="block text-gray-700 text-sm font-bold mb-2">Registration Window Password (if applicable)</label>
                        <input type="password" name="window_password" id="window_password"
                               class="border @error('window_password') border-red-500 @enderror rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent focus:ring-offset-2 disabled:opacity-50"
                               placeholder="Enter window password if provided">
                        @error('window_password') <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="mt-8 flex items-center justify-center">
                    <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-opacity-75 transition duration-150 ease-in-out shadow-md hover:shadow-lg">
                        Register
                    </button>
                </div>
                <p class="text-center text-gray-500 text-xs mt-6">
                    Already have an account? <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 font-semibold">Sign in</a>.
                </p>
            </form>
        </div>
        <p class="text-center text-gray-600 text-xs mt-6 sm:mt-8">
            &copy;{{ date('Y') }} {{ config('app.name', 'EPATV Job Portal') }}. All rights reserved.
        </p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleInput = document.querySelector('input[name="role"]');
    const employerFields = document.getElementById('employer_fields');
    const studentFields = document.getElementById('student_fields');

    // Phone input fields
    const studentPhoneInput = studentFields.querySelector('input[id="student_phone"]');
    const employerPhoneInput = employerFields.querySelector('input[id="employer_phone"]');

    function toggleFields(selectedRole) {
        if (!selectedRole) return;

        if (selectedRole === 'employer') {
            employerFields.classList.remove('hidden');
            studentFields.classList.add('hidden');
            // Enable employer phone, disable student phone name
            employerPhoneInput.setAttribute('name', 'phone');
            studentPhoneInput.setAttribute('name', 'student_phone_disabled');
        } else { // student or candidate
            employerFields.classList.add('hidden');
            studentFields.classList.remove('hidden');
            // Enable student phone, disable employer phone name
            studentPhoneInput.setAttribute('name', 'phone');
            employerPhoneInput.setAttribute('name', 'employer_phone_disabled');
        }
    }

    const currentRole = roleInput ? roleInput.value : null;
    if (currentRole) { // Ensure currentRole is not null before calling toggleFields
      toggleFields(currentRole);
    } else {
      // Fallback or default behavior if role is not set, e.g., show student fields
      // This case should ideally not happen if $type is always passed from controller
      console.warn("Role not specified, defaulting to student fields display logic.");
      toggleFields('student');
    }
});
</script>
@endsection
