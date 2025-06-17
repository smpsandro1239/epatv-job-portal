@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-4xl font-extrabold text-gray-800 mb-10 text-center">Superadmin Dashboard</h1>

    <!-- Stats Cards -->
    <div class="mb-12">
        <h2 class="text-2xl font-semibold text-gray-700 mb-6">Key Metrics</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            {{-- Total Users --}}
            <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 flex items-start space-x-4">
                <div class="flex-shrink-0 h-12 w-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-600 mb-1">Total Users</h3>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['total_users'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Excluding Superadmins</p>
                </div>
            </div>
            {{-- Total Jobs --}}
            <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 flex items-start space-x-4">
                <div class="flex-shrink-0 h-12 w-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-600 mb-1">Total Jobs</h3>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['total_jobs'] }}</p>
                </div>
            </div>
            {{-- Pending Registrations --}}
            <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 flex items-start space-x-4">
                 <div class="flex-shrink-0 h-12 w-12 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-600 mb-1">Pending Registrations</h3>
                    <p class="text-3xl font-bold text-yellow-500">{{ $stats['pending_registrations'] }}</p>
                </div>
            </div>
            {{-- Total Applications --}}
            <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 flex items-start space-x-4">
                <div class="flex-shrink-0 h-12 w-12 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-600 mb-1">Total Applications</h3>
                    <p class="text-3xl font-bold text-indigo-600">{{ $stats['total_applications'] }}</p>
                </div>
            </div>
            {{-- Students/Candidates --}}
            <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 flex items-start space-x-4">
                <div class="flex-shrink-0 h-12 w-12 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 14l9-5-9-5-9 5 9 5z"></path><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-600 mb-1">Students / Candidates</h3>
                    <p class="text-3xl font-bold text-purple-600">{{ $stats['students_count'] }}</p>
                </div>
            </div>
            {{-- Employers --}}
            <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 flex items-start space-x-4">
                <div class="flex-shrink-0 h-12 w-12 bg-pink-100 text-pink-600 rounded-full flex items-center justify-center">
                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-600 mb-1">Employers</h3>
                    <p class="text-3xl font-bold text-pink-600">{{ $stats['employers_count'] }}</p>
                </div>
            </div>
            {{-- Students with CVs --}}
            <div class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 flex items-start space-x-4">
                <div class="flex-shrink-0 h-12 w-12 bg-teal-100 text-teal-600 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-600 mb-1">Students with CVs</h3>
                    <p class="text-3xl font-bold text-teal-600">{{ $stats['students_with_cv_count'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div>
        <h2 class="text-2xl font-semibold text-gray-700 mb-6">Analytics Overview</h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-lg h-96"> {{-- Added h-96 for defined height --}}
                <h3 class="text-xl font-semibold text-gray-700 mb-4">Jobs by Month</h3>
                <canvas id="jobsByMonthChart"></canvas>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg h-96">
                <h3 class="text-xl font-semibold text-gray-700 mb-4">Jobs by Area of Interest (Top 5)</h3>
                <canvas id="jobsByAreaChart"></canvas>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg h-96">
                <h3 class="text-xl font-semibold text-gray-700 mb-4">Jobs by Location (Top 5)</h3>
                <canvas id="jobsByLocationChart"></canvas>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-lg h-96">
                <h3 class="text-xl font-semibold text-gray-700 mb-4">Jobs by Contract Type (Top 5)</h3>
                <canvas id="jobsByContractTypeChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Jobs by Month Chart
    const jobsByMonthCtx = document.getElementById('jobsByMonthChart')?.getContext('2d');
    if (jobsByMonthCtx) {
        const jobsByMonthData = @json($stats['jobs_by_month'] ?? []);
        new Chart(jobsByMonthCtx, {
            type: 'line',
            data: {
                labels: jobsByMonthData.map(item => item.month),
                datasets: [{
                    label: 'Jobs Posted',
                    data: jobsByMonthData.map(item => item.total),
                    borderColor: 'rgb(54, 162, 235)', // Blue
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    // Jobs by Area Chart (Pie or Doughnut)
    const jobsByAreaCtx = document.getElementById('jobsByAreaChart')?.getContext('2d');
    if (jobsByAreaCtx) {
        const jobsByAreaData = @json($stats['jobs_by_area_top5'] ?? []);
        new Chart(jobsByAreaCtx, {
            type: 'doughnut',
            data: {
                labels: jobsByAreaData.map(item => item.area_name),
                datasets: [{
                    label: 'Jobs by Area',
                    data: jobsByAreaData.map(item => item.total),
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#C9CBCF']
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    // Jobs by Location Chart (Bar)
    const jobsByLocationCtx = document.getElementById('jobsByLocationChart')?.getContext('2d');
    if (jobsByLocationCtx) {
        const jobsByLocationData = @json($stats['jobs_by_location_top5'] ?? []);
        new Chart(jobsByLocationCtx, {
            type: 'bar',
            data: {
                labels: jobsByLocationData.map(item => item.location),
                datasets: [{
                    label: 'Jobs by Location',
                    data: jobsByLocationData.map(item => item.total),
                    backgroundColor: '#4BC0C0' // Teal
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, indexAxis: 'y' }
        });
    }

    // Jobs by Contract Type (Pie or Bar)
    const jobsByContractTypeCtx = document.getElementById('jobsByContractTypeChart')?.getContext('2d');
    if (jobsByContractTypeCtx) {
        const jobsByContractTypeData = @json($stats['jobs_by_contract_type_top5'] ?? []);
        new Chart(jobsByContractTypeCtx, {
            type: 'pie',
            data: {
                labels: jobsByContractTypeData.map(item => item.contract_type),
                datasets: [{
                    label: 'Jobs by Contract Type',
                    data: jobsByContractTypeData.map(item => item.total),
                    backgroundColor: ['#FF9F40', '#FFCD56', '#FF6384', '#36A2EB', '#9966FF', '#4BC0C0', '#C9CBCF']
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }
});
</script>
@endpush
