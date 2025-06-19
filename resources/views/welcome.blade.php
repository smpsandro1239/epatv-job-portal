@extends('layouts.app')

@section('content')
    {{-- Hero Section --}}
    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white">
        <div class="container mx-auto px-6 py-24 text-center">
            <h1 class="text-5xl font-extrabold mb-4 leading-tight">
                Find Your Future at {{ config('app.name', 'EPATV Job Portal') }}
            </h1>
            <p class="text-xl text-blue-100 mb-12 max-w-2xl mx-auto">
                Connecting talented students and graduates with exciting opportunities from leading employers. Your next career move starts here.
            </p>
            <p class="text-lg text-blue-200 mt-4 mb-8"><span id="active-job-count-placeholder">Loading...</span> vagas disponíveis!</p>
            <div class="space-x-4">
                <a href="{{ route('jobs.index') }}"
                   class="bg-yellow-400 hover:bg-yellow-500 text-gray-800 font-bold py-3 px-8 rounded-lg text-lg shadow-lg transform hover:scale-105 transition duration-300">
                    Browse Jobs
                </a>
                @guest
                <a href="{{ route('register') }}"
                   class="bg-white hover:bg-gray-100 text-blue-600 font-bold py-3 px-8 rounded-lg text-lg shadow-lg transform hover:scale-105 transition duration-300">
                    Sign Up Now
                </a>
                @endguest
                @auth
                 <a href="{{ Auth::user()->role === 'student' ? route('student.profile.show') : (Auth::user()->role === 'employer' ? route('employer.profile.show') : route('admin.dashboard')) }}"
                   class="bg-white hover:bg-gray-100 text-blue-600 font-bold py-3 px-8 rounded-lg text-lg shadow-lg transform hover:scale-105 transition duration-300">
                    Go to Your Dashboard
                </a>
                @endauth
            </div>
        </div>
    </div>

    {{-- Features Section --}}
    <div class="py-16 bg-gray-50">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">Why Choose Us?</h2>
            <div class="grid md:grid-cols-3 gap-10">
                <div class="bg-white p-8 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="flex items-center justify-center h-16 w-16 bg-blue-100 text-blue-600 rounded-full mb-6 mx-auto">
                        {{-- Placeholder for an icon --}}
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3 text-center">For Students & Graduates</h3>
                    <p class="text-gray-600 text-center">
                        Discover job openings, internships, and career opportunities perfectly matched to your skills and aspirations. Build your profile and get noticed.
                    </p>
                </div>
                <div class="bg-white p-8 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
                     <div class="flex items-center justify-center h-16 w-16 bg-green-100 text-green-600 rounded-full mb-6 mx-auto">
                        {{-- Placeholder for an icon --}}
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3 text-center">For Employers</h3>
                    <p class="text-gray-600 text-center">
                        Access a pool of talented individuals from EPATV. Post job vacancies, manage applications, and find the perfect candidates for your company.
                    </p>
                </div>
                <div class="bg-white p-8 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <div class="flex items-center justify-center h-16 w-16 bg-indigo-100 text-indigo-600 rounded-full mb-6 mx-auto">
                        {{-- Placeholder for an icon --}}
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3 text-center">Targeted Opportunities</h3>
                    <p class="text-gray-600 text-center">
                        Our platform focuses on connecting EPATV students and alumni with relevant employers, ensuring meaningful matches for both parties.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        fetch('/api/jobs/active-count')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const countElement = document.getElementById('active-job-count-placeholder');
                if (countElement && data.active_job_count !== undefined) {
                    countElement.textContent = data.active_job_count;
                } else if (countElement) {
                    countElement.textContent = 'N/A';
                }
            })
            .catch(error => {
                console.error('Error fetching active job count:', error);
                const countElement = document.getElementById('active-job-count-placeholder');
                if (countElement) {
                    countElement.textContent = '-'; // Or some other error indicator
                }
            });
    });
</script>
@endpush
