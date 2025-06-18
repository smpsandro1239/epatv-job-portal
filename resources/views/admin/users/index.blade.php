@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-bold text-gray-800 mb-4 sm:mb-0">User Management</h1>
        {{-- Add any primary actions here if needed, e.g., "Create User" --}}
    </div>

    {{-- Overall Statistics --}}
    <div class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h3 class="text-lg font-semibold text-gray-600 mb-1">Total Users</h3>
            <p class="text-3xl font-bold text-blue-600">{{ $stats['total_users_overall'] ?? 'N/A' }}</p>
            <p class="text-xs text-gray-500 mt-1">Excluding Superadmins</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h3 class="text-lg font-semibold text-gray-600 mb-1">With CV</h3>
            <p class="text-3xl font-bold text-green-600">{{ $stats['total_with_cv_overall'] ?? 'N/A' }}</p>
            <p class="text-xs text-gray-500 mt-1">Users who have uploaded a CV</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h3 class="text-lg font-semibold text-gray-600 mb-1">Without CV</h3>
            <p class="text-3xl font-bold text-yellow-500">{{ $stats['total_without_cv_overall'] ?? 'N/A' }}</p>
            <p class="text-xs text-gray-500 mt-1">Users who have not uploaded a CV</p>
        </div>
    </div>

    {{-- Session messages are handled by layouts.app --}}

    <!-- Filter Form -->
    <form action="{{ route('admin.users.index') }}" method="GET" class="mb-10 p-6 bg-white shadow-lg rounded-lg border border-gray-200">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 items-end"> {{-- Adjusted grid columns --}}
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Filter by Role</label>
                <select name="role" id="role"
                        class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Roles (except Superadmin)</option>
                    @foreach($roles as $roleValue) {{-- Assuming $roles is passed from controller --}}
                        <option value="{{ $roleValue }}" {{ (isset($filters['role']) && $filters['role'] == $roleValue) ? 'selected' : '' }}>
                            {{ ucfirst($roleValue) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="registration_status" class="block text-sm font-medium text-gray-700 mb-1">Filter by Status</label>
                <select name="registration_status" id="registration_status"
                        class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Statuses</option>
                     @foreach($statuses as $statusValue) {{-- Assuming $statuses is passed from controller --}}
                        <option value="{{ $statusValue }}" {{ (isset($filters['registration_status']) && $filters['registration_status'] == $statusValue) ? 'selected' : '' }}>
                            {{ ucfirst($statusValue) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="filter_cv_status" class="block text-sm font-medium text-gray-700 mb-1">Filter by CV Status</label>
                <select name="filter_cv_status" id="filter_cv_status"
                        class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach($cvStatusOptions as $value => $label)
                        <option value="{{ $value }}" {{ (isset($filters['filter_cv_status']) && $filters['filter_cv_status'] == $value) ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex space-x-3">
                <button type="submit"
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-75 transition duration-150">
                    Apply Filters
                </button>
                 <a href="{{ route('admin.users.index') }}"
                   class="w-full text-center bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-75 transition duration-150">
                    Clear
                </a>
            </div>
        </div>
    </form>

    @if($users->isEmpty())
        <div class="text-center py-12 bg-white shadow-md rounded-lg">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            <h3 class="mt-2 text-lg font-medium text-gray-900">No Users Found</h3>
            <p class="mt-1 text-sm text-gray-500">
                No users match your current filter criteria.
            </p>
        </div>
    @else
        <div class="bg-white shadow-xl rounded-lg overflow-x-auto">
            <table class="min-w-full w-full table-auto">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr class="text-gray-700 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Name</th>
                        <th class="py-3 px-6 text-left">Email</th>
                        <th class="py-3 px-6 text-left">Phone</th> <!-- New -->
                        <th class="py-3 px-6 text-left">Role</th>
                        <th class="py-3 px-6 text-center">CV Status</th> <!-- New -->
                        <th class="py-3 px-6 text-center">Status</th>
                        <th class="py-3 px-6 text-center">Registered On</th>
                        <th class="py-3 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @foreach ($users as $user)
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors duration-150">
                            <td class="py-4 px-6 text-left whitespace-nowrap font-medium">{{ $user->name }}</td>
                            <td class="py-4 px-6 text-left">{{ $user->email }}</td>
                            <td class="py-4 px-6 text-left">{{ $user->phone ?? 'N/A' }}</td> <!-- New -->
                            <td class="py-4 px-6 text-left">{{ ucfirst($user->role) }}</td>
                            <td class="py-4 px-6 text-center"> <!-- New -->
                                @if($user->cv)
                                    <a href="{{ Storage::url($user->cv) }}" target="_blank" class="text-blue-500 hover:text-blue-700 hover:underline">View CV</a>
                                @else
                                    <span class="text-gray-400 italic">Not Available</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="py-1 px-3 rounded-full text-xs font-semibold
                                    @if($user->registration_status == 'approved') bg-green-100 text-green-700 @endif
                                    @if($user->registration_status == 'pending') bg-yellow-100 text-yellow-700 @endif
                                    @if($user->registration_status == 'rejected') bg-red-100 text-red-700 @endif
                                    @if(!in_array($user->registration_status, ['approved', 'pending', 'rejected'])) bg-gray-100 text-gray-700 @endif">
                                    {{ ucfirst($user->registration_status) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">{{ $user->created_at->format('M d, Y H:i') }}</td>
                            <td class="py-4 px-6 text-center">
                                @if($user->registration_status == 'pending')
                                    <form action="{{ route('admin.users.approve', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to approve this user: {{ addslashes($user->name) }}?');">
                                        @csrf
                                        <button type="submit" class="text-xs bg-green-500 hover:bg-green-600 text-white font-semibold py-1 px-3 rounded-lg shadow-sm hover:shadow-md transition duration-150 inline-flex items-center">
                                            <svg class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Approve
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400 italic">N/A</span>
                                @endif
                                {{-- Future actions: Edit, Delete, View Profile --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-8">
            {{ $users->appends(request()->query())->links() }} {{-- Ensure colspan is updated if "No users found" row uses it --}}
        </div>
    @endif
</div>
@endsection
