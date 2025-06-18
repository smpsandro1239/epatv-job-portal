<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'EPATV Job Portal') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    {{-- Add any other global styles or scripts here --}}
    @stack('styles')
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div id="app" class="min-h-screen flex flex-col">
        <!-- Navigation Bar -->
        <nav class="bg-blue-600 text-white shadow-md">
            <div class="container mx-auto px-4">
                <div class="flex justify-between items-center py-4">
                    <a href="{{ route('home') }}" class="text-xl font-bold">{{ config('app.name', 'Job Portal') }}</a>

                    <div class="flex items-center space-x-4"> <!-- Simplified this div -->
                        <a href="{{ route('jobs.index') }}" class="px-2 py-1 hover:text-blue-200 rounded-md">Jobs</a>

                        @guest
                            <a href="{{ route('login') }}" class="px-2 py-1 hover:text-blue-200 rounded-md">Login</a>
                            <a href="{{ route('register') }}" class="px-2 py-1 hover:text-blue-200 rounded-md">Register</a>
                        @endguest

                        @auth
                            @if(Auth::user()->role === 'student')
                                <a href="{{ route('student.profile.show') }}" class="px-2 py-1 hover:text-blue-200 rounded-md">My Profile</a>
                                <a href="{{ route('student.applications.index') }}" class="px-2 py-1 hover:text-blue-200 rounded-md">My Applications</a>
                            @endif

                            @if(Auth::user()->role === 'employer')
                                <a href="{{ route('employer.profile.show') }}" class="px-2 py-1 hover:text-blue-200 rounded-md">Company Profile</a>
                                <a href="{{ route('employer.jobs.index') }}" class="px-2 py-1 hover:text-blue-200 rounded-md">My Jobs</a>
                                <a href="{{ route('employer.applications.index') }}" class="px-2 py-1 hover:text-blue-200 rounded-md">Job Applications</a>
                            @endif

                            @if(Auth::user()->role === 'superadmin')
                                <a href="{{ route('admin.dashboard') }}" class="px-2 py-1 hover:text-blue-200 rounded-md">Admin Dashboard</a>
                                <a href="{{ route('admin.users.index') }}" class="px-2 py-1 hover:text-blue-200 rounded-md">User Management</a>
                                <a href="{{ route('admin.regwindow.edit') }}" class="px-2 py-1 hover:text-blue-200 rounded-md">Reg. Window</a>
                            @endif

                            <a href="{{ route('notifications.index') }}" class="px-2 py-1 hover:text-blue-200 rounded-md relative">
                                Notifications
                                @php $unreadNotificationsCount = Auth::user()->unreadNotifications()->count(); @endphp
                                @if($unreadNotificationsCount > 0)
                                    <span class="absolute -top-1 -right-2.5 ml-1 inline-block py-0.5 px-1.5 leading-none text-center whitespace-nowrap align-baseline font-bold bg-red-500 text-white rounded-full text-xs">
                                        {{ $unreadNotificationsCount }}
                                    </span>
                                @endif
                            </a>

                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="px-2 py-1 hover:text-blue-200 rounded-md">Logout</button>
                            </form>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-grow container mx-auto px-4 py-8">
            <!-- Global Session Messages -->
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-md rounded-md" role="alert">
                    <p class="font-bold">Success</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 shadow-md rounded-md" role="alert">
                    <p class="font-bold">Error</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif
            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 shadow-md rounded-md" role="alert">
                    <p class="font-bold">Please fix the following errors:</p>
                    <ul class="list-disc ml-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-700 text-white text-center p-4 mt-auto">
            &copy; {{ date('Y') }} {{ config('app.name', 'Job Portal') }}. All rights reserved.
        </footer>
    </div>
    @stack('scripts')
</body>
</html>
