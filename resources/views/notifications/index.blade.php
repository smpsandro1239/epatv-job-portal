@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">My Notifications</h1>
        @if($unreadCount > 0)
            <form action="{{ route('notifications.markallasread') }}" method="POST" class="inline-block">
                @csrf
                <button type="submit" class="text-sm text-blue-600 hover:underline">Mark all as read ({{ $unreadCount }} unread)</button>
            </form>
        @endif
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if($notifications->isEmpty())
        <p class="text-gray-600">You have no notifications yet.</p>
    @else
        <div class="space-y-4">
            @foreach ($notifications as $notification)
                <div class="p-4 rounded-lg shadow {{ is_null($notification->read_at) ? 'bg-blue-50 border border-blue-200' : 'bg-white border border-gray-200' }}">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-800 {{ is_null($notification->read_at) ? 'font-semibold' : '' }}">
                                {{ $notification->data['message'] ?? 'Notification details are missing.' }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $notification->created_at->diffForHumans() }}
                                @if($notification->read_at)
                                    (Read: {{ $notification->read_at->diffForHumans() }})
                                @endif
                            </p>
                        </div>
                        @if(is_null($notification->read_at))
                            <form action="{{ route('notifications.read', $notification) }}" method="POST" class="ml-4 flex-shrink-0">
                                @csrf
                                <button type="submit" class="text-xs bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline">
                                    Mark as read
                                </button>
                            </form>
                        @endif
                    </div>
                    @if(isset($notification->data['job_id']) && Route::has('jobs.show')) {{-- Check if jobs.show route exists --}}
                        <div class="mt-2">
                            <a href="{{ route('jobs.show', $notification->data['job_id']) }}" class="text-sm text-blue-600 hover:underline">View Job Details &raquo;</a>
                        </div>
                    @endif
                     @if(isset($notification->data['student_email'])) {{-- For admin notification about pending student --}}
                        <div class="mt-2">
                            <p class="text-sm text-gray-700">Student: {{ $notification->data['student_name'] ?? '' }} ({{ $notification->data['student_email'] }})</p>
                            @if(Route::has('admin.users.index')) {{-- Assuming a route to view users in admin panel --}}
                                <a href="{{ route('admin.users.index', ['email' => $notification->data['student_email']]) }}" class="text-sm text-blue-600 hover:underline">View User &raquo;</a>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
