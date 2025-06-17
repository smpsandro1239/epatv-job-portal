@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-10">
    <div class="bg-white shadow-2xl rounded-lg overflow-hidden">
        {{-- Job Header --}}
        <div class="bg-gradient-to-r from-gray-700 via-gray-800 to-gray-900 text-white p-8 md:p-12">
            <h1 class="text-4xl md:text-5xl font-extrabold mb-3">{{ $job->title }}</h1>
            @if($job->company)
                <p class="text-xl md:text-2xl text-gray-300 mb-1">
                    <a href="{{ $job->company->company_website ? Str::startsWith($job->company->company_website, ['http://', 'https://']) ? $job->company->company_website : '//'.$job->company->company_website : '#' }}" target="_blank" rel="noopener noreferrer" class="hover:underline">
                        {{ $job->company->company_name ?? $job->company->name }}
                    </a>
                </p>
            @endif
            @if($job->location)
                <p class="text-gray-400 text-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 019.9-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                    {{ $job->location }}
                </p>
            @endif
        </div>

        <div class="p-8 md:p-12 grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main Job Details Column --}}
            <div class="lg:col-span-2">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6 border-b pb-3">Job Overview</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Area of Interest</h3>
                        <p class="text-lg text-gray-700">{{ $job->areaOfInterest->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Contract Type</h3>
                        <p class="text-lg text-gray-700">{{ $job->contract_type ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Salary</h3>
                        <p class="text-lg text-gray-700">{{ $job->salary ?? 'Not specified' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-1">Expiration Date</h3>
                        <p class="text-lg text-red-600 font-semibold">{{ $job->expiration_date ? \Carbon\Carbon::parse($job->expiration_date)->format('M d, Y') : 'Not specified' }}</p>
                    </div>
                </div>

                <h3 class="text-xl font-semibold text-gray-800 mt-8 mb-3">Full Job Description</h3>
                <div class="prose max-w-none text-gray-700 leading-relaxed">
                    {!! nl2br(e($job->description)) !!}
                </div>

                {{-- Action Buttons for Students --}}
                @auth
                    @if(Auth::user()->role === 'student')
                        <div class="mt-10 pt-6 border-t">
                            {{-- Apply Now Button - Placeholder for API interaction or form --}}
                            <button onclick="handleApplyNow({{ $job->id }})"
                                    class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-8 rounded-lg text-lg shadow-md hover:shadow-lg transition duration-150 mr-4">
                                Apply Now
                            </button>

                            {{-- Save Job Button - Placeholder for API interaction --}}
                            <button id="saveJobBtn" onclick="toggleSaveJob({{ $job->id }})"
                                    data-job-id="{{ $job->id }}"
                                    class="{{ $isSaved ? 'bg-gray-400 hover:bg-gray-500' : 'bg-blue-500 hover:bg-blue-600' }} text-white font-semibold py-3 px-6 rounded-lg text-lg shadow-md hover:shadow-lg transition duration-150">
                                {{ $isSaved ? 'Unsave Job' : 'Save Job' }}
                            </button>
                        </div>
                    @endif
                @else {{-- Guest User --}}
                    <div class="mt-10 pt-6 border-t">
                         <a href="{{ route('login', ['redirect' => url()->current()]) }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-8 rounded-lg text-lg shadow-md hover:shadow-lg transition duration-150 mr-4">
                            Login to Apply
                        </a>
                         <a href="{{ route('login', ['redirect' => url()->current()]) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg text-lg shadow-md hover:shadow-lg transition duration-150">
                            Login to Save
                        </a>
                    </div>
                @endauth
            </div>

            {{-- Sidebar: Company Information & Actions for Employer --}}
            <div class="lg:col-span-1">
                <div class="bg-gray-50 p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">About the Company</h3>
                    @if($job->company)
                        @if($job->company->company_logo)
                            <img src="{{ Storage::url($job->company->company_logo) }}" alt="{{ $job->company->company_name ?? $job->company->name }} Logo" class="w-24 h-24 object-contain rounded-md mx-auto mb-4 shadow">
                        @endif
                        <p class="text-lg font-medium text-gray-900 text-center">{{ $job->company->company_name ?? $job->company->name }}</p>
                        @if($job->company->company_website)
                            <p class="text-sm text-blue-600 hover:underline text-center mb-3">
                                <a href="{{ $job->company->company_website ? Str::startsWith($job->company->company_website, ['http://', 'https://']) ? $job->company->company_website : '//'.$job->company->company_website : '#' }}" target="_blank" rel="noopener noreferrer">
                                    Visit Website
                                </a>
                            </p>
                        @endif
                        <p class="text-sm text-gray-600 leading-relaxed">
                            {{ Str::limit($job->company->company_description ?? 'No description provided.', 150) }}
                        </p>
                    @else
                        <p class="text-gray-600 italic">Company details not available.</p>
                    @endif
                </div>

                @auth
                    @if(Auth::user()->id === $job->company_id && Auth::user()->role === 'employer')
                        <div class="mt-6 text-center">
                            <a href="{{ route('employer.jobs.edit', $job->id) }}"
                               class="inline-block w-full bg-yellow-500 hover:bg-yellow-600 text-gray-800 font-semibold py-2 px-4 rounded-lg shadow-md transition duration-150">
                                Edit This Job Posting
                            </a>
                        </div>
                    @endif
                @endauth
                 <div class="mt-8 text-center">
                    <a href="{{ route('jobs.index') }}" class="text-blue-600 hover:underline">&laquo; Back to All Jobs</a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Placeholder for API interactions
function handleApplyNow(jobId) {
    @auth
        @if(Auth::user()->role === 'student')
            // Logic to initiate application, perhaps redirect to a form or use API
            // For now, let's assume an API call might be made or a modal shown
            // Example: check if student has CV/profile complete before applying
            alert('Apply Now clicked for Job ID: ' + jobId + '. This would ideally trigger an API call or a dedicated application form.');
            // Example API call structure (requires an endpoint like /api/student/applications)
            /*
            fetch('/api/apply', { // Assuming this is the endpoint from ApplicationController
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), // If using web session for API
                    'Authorization': 'Bearer your_api_token_here' // If using JWT
                },
                body: JSON.stringify({ job_id: jobId, cover_letter: 'Optional cover letter here...' })
            })
            .then(response => response.json())
            .then(data => {
                if(data.message) alert(data.message);
                // Potentially update UI, e.g., change button to "Applied"
            })
            .catch(error => console.error('Error applying:', error));
            */
        @else
            alert('Only students can apply for jobs.');
        @endif
    @else
        window.location.href = "{{ route('login', ['redirect' => url()->current()]) }}";
    @endauth
}

function toggleSaveJob(jobId) {
    @auth
        @if(Auth::user()->role === 'student')
            const button = document.getElementById('saveJobBtn');
            const isCurrentlySaved = button.textContent.trim() === 'Unsave Job';

            // Optimistic UI update
            button.textContent = isCurrentlySaved ? 'Save Job' : 'Unsave Job';
            button.classList.toggle('bg-gray-400', !isCurrentlySaved);
            button.classList.toggle('hover:bg-gray-500', !isCurrentlySaved);
            button.classList.toggle('bg-blue-500', isCurrentlySaved);
            button.classList.toggle('hover:bg-blue-600', isCurrentlySaved);

            fetch(`/api/student/jobs/${jobId}/save`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), // For web session based API auth
                    'Authorization': `Bearer ${localStorage.getItem('api_token')}` // Assuming token stored in localStorage
                },
            })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                // Update button based on actual response
                button.textContent = data.is_saved ? 'Unsave Job' : 'Save Job';
                button.classList.toggle('bg-gray-400', !data.is_saved);
                button.classList.toggle('hover:bg-gray-500', !data.is_saved);
                button.classList.toggle('bg-blue-500', data.is_saved);
                button.classList.toggle('hover:bg-blue-600', data.is_saved);
                // alert(data.message); // Optional feedback
            })
            .catch(error => {
                console.error('Error saving job:', error);
                // Revert UI on error
                button.textContent = isCurrentlySaved ? 'Unsave Job' : 'Save Job';
                button.classList.toggle('bg-gray-400', isCurrentlySaved);
                button.classList.toggle('hover:bg-gray-500', isCurrentlySaved);
                button.classList.toggle('bg-blue-500', !isCurrentlySaved);
                button.classList.toggle('hover:bg-blue-600', !isCurrentlySaved);
                alert('Could not update job save status. Please try again.');
            });
        @else
             alert('Only students can save jobs.');
        @endif
    @else
        window.location.href = "{{ route('login', ['redirect' => url()->current()]) }}";
    @endauth
}
</script>
@endpush
@endsection
