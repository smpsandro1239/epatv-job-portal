@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-6">User Management</h1>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filter Form -->
    <form action="{{ route('admin.users.index') }}" method="GET" class="mb-6 p-4 border rounded bg-gray-50">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                <select name="role" id="role" class="mt-1 block w-full p-2 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">All Roles</option>
                    @foreach($roles as $roleValue)
                        <option value="{{ $roleValue }}" {{ (isset($filters['role']) && $filters['role'] == $roleValue) ? 'selected' : '' }}>
                            {{ ucfirst($roleValue) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="registration_status" class="block text-sm font-medium text-gray-700">Registration Status</label>
                <select name="registration_status" id="registration_status" class="mt-1 block w-full p-2 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">All Statuses</option>
                     @foreach($statuses as $statusValue)
                        <option value="{{ $statusValue }}" {{ (isset($filters['registration_status']) && $filters['registration_status'] == $statusValue) ? 'selected' : '' }}>
                            {{ ucfirst($statusValue) }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Filter</button>
            <a href="{{ route('admin.users.index') }}" class="ml-2 px-4 py-2 bg-gray-300 text-black rounded hover:bg-gray-400">Clear Filters</a>
        </div>
    </form>

    @if($users->isEmpty())
        <p>No users found matching your criteria.</p>
    @else
        <div class="bg-white shadow-md rounded my-6">
            <table class="min-w-max w-full table-auto">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Name</th>
                        <th class="py-3 px-6 text-left">Email</th>
                        <th class="py-3 px-6 text-left">Role</th>
                        <th class="py-3 px-6 text-center">Status</th>
                        <th class="py-3 px-6 text-center">Registered On</th>
                        <th class="py-3 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @foreach ($users as $user)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left whitespace-nowrap">{{ $user->name }}</td>
                            <td class="py-3 px-6 text-left">{{ $user->email }}</td>
                            <td class="py-3 px-6 text-left">{{ ucfirst($user->role) }}</td>
                            <td class="py-3 px-6 text-center">
                                <span class="py-1 px-3 rounded-full text-xs
                                    @if($user->registration_status == 'approved') bg-green-200 text-green-700 @endif
                                    @if($user->registration_status == 'pending') bg-yellow-200 text-yellow-700 @endif
                                    @if($user->registration_status == 'rejected') bg-red-200 text-red-700 @endif">
                                    {{ ucfirst($user->registration_status) }}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-center">{{ $user->created_at->format('Y-m-d H:i') }}</td>
                            <td class="py-3 px-6 text-center">
                                @if($user->registration_status == 'pending')
                                    <form action="{{ route('admin.users.approve', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to approve this user?');">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900">Approve</button>
                                    </form>
                                @else
                                    N/A
                                @endif
                                <!-- Add other actions like edit/delete user if needed in future -->
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            {{ $users->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
