@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-8 text-gray-800">Superadmin Dashboard</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold text-gray-700 mb-2">Total Users</h2>
            <p class="text-4xl font-bold text-blue-600">{{ $stats['total_users'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Excluding Superadmins</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold text-gray-700 mb-2">Total Jobs</h2>
            <p class="text-4xl font-bold text-green-600">{{ $stats['total_jobs'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold text-gray-700 mb-2">Pending Registrations</h2>
            <p class="text-4xl font-bold text-yellow-500">{{ $stats['pending_registrations'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold text-gray-700 mb-2">Total Applications</h2>
            <p class="text-4xl font-bold text-indigo-600">{{ $stats['total_applications'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold text-gray-700 mb-2">Students / Candidates</h2>
            <p class="text-3xl font-bold text-purple-600">{{ $stats['students_count'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold text-gray-700 mb-2">Employers</h2>
            <p class="text-3xl font-bold text-pink-600">{{ $stats['employers_count'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold text-gray-700 mb-2">Students with CVs</h2>
            <p class="text-3xl font-bold text-teal-600">{{ $stats['students_with_cv_count'] }}</p>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Jobs by Month</h2>
            <canvas id="jobsByMonthChart"></canvas>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Jobs by Area of Interest (Top 5)</h2>
            <canvas id="jobsByAreaChart"></canvas>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Jobs by Location (Top 5)</h2>
            <canvas id="jobsByLocationChart"></canvas>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">Jobs by Contract Type (Top 5)</h2>
            <canvas id="jobsByContractTypeChart"></canvas>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Jobs by Month Chart
    const jobsByMonthCtx = document.getElementById('jobsByMonthChart').getContext('2d');
    const jobsByMonthData = @json($stats['jobs_by_month']);
    new Chart(jobsByMonthCtx, {
        type: 'line', // or 'bar'
        data: {
            labels: jobsByMonthData.map(item => item.month),
            datasets: [{
                label: 'Jobs Posted',
                data: jobsByMonthData.map(item => item.total),
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1,
                fill: false
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // Jobs by Area Chart (Pie or Doughnut)
    const jobsByAreaCtx = document.getElementById('jobsByAreaChart').getContext('2d');
    const jobsByAreaData = @json($stats['jobs_by_area_top5']); // Using top5 for pie chart
    new Chart(jobsByAreaCtx, {
        type: 'doughnut',
        data: {
            labels: jobsByAreaData.map(item => item.area_name),
            datasets: [{
                label: 'Jobs by Area',
                data: jobsByAreaData.map(item => item.total),
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // Jobs by Location Chart (Bar)
    const jobsByLocationCtx = document.getElementById('jobsByLocationChart').getContext('2d');
    const jobsByLocationData = @json($stats['jobs_by_location_top5']);
    new Chart(jobsByLocationCtx, {
        type: 'bar',
        data: {
            labels: jobsByLocationData.map(item => item.location),
            datasets: [{
                label: 'Jobs by Location',
                data: jobsByLocationData.map(item => item.total),
                backgroundColor: '#36A2EB'
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, indexAxis: 'y' } // Horizontal bar chart
    });

    // Jobs by Contract Type (Pie or Bar)
    const jobsByContractTypeCtx = document.getElementById('jobsByContractTypeChart').getContext('2d');
    const jobsByContractTypeData = @json($stats['jobs_by_contract_type_top5']);
    new Chart(jobsByContractTypeCtx, {
        type: 'pie',
        data: {
            labels: jobsByContractTypeData.map(item => item.contract_type),
            datasets: [{
                label: 'Jobs by Contract Type',
                data: jobsByContractTypeData.map(item => item.total),
                backgroundColor: ['#FF9F40', '#FFCD56', '#4BC0C0', '#9966CC', '#C9CBCF']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
});
</script>
@endpush
