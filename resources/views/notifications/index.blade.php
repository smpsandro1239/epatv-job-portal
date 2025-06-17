@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-8 pb-4 border-b border-gray-200">
        <h1 class="text-3xl font-bold text-gray-800 mb-4 sm:mb-0">My Notifications</h1>
        @if($unreadCount > 0)
            <form action="{{ route('notifications.markallasread') }}" method="POST" class="inline-block">
                @csrf
                <button type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md text-sm transition duration-150">
                    Mark all as read ({{ $unreadCount }} unread)
                </button>
            </form>
        @endif
    </div>

    {{-- Session messages already handled by layouts.app --}}

    @if($notifications->isEmpty())
        <div class="text-center py-12 bg-white shadow-md rounded-lg">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            <h3 class="mt-2 text-lg font-medium text-gray-900">No Notifications Yet</h3>
            <p class="mt-1 text-sm text-gray-500">
                We'll let you know when there's something new for you.
            </p>
        </div>
    @else
        <div class="space-y-5">
            @foreach ($notifications as $notification)
                <div class="bg-white shadow-lg rounded-lg overflow-hidden
                            {{ is_null($notification->read_at) ? 'border-l-4 border-blue-500' : 'border-l-4 border-transparent' }}
                            hover:shadow-xl transition-shadow duration-300">
                    <div class="p-5 md:p-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mr-4">
                                {{-- Generic Notification Icon --}}
                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                </div>
                            </div>
                            <div class="flex-grow">
                                <p class="text-gray-800 {{ is_null($notification->read_at) ? 'font-semibold' : 'text-gray-600' }}">
                                    {{ $notification->data['message'] ?? 'Notification details are missing.' }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $notification->created_at->diffForHumans() }}
                                    @if($notification->read_at)
                                        <span class="text-green-600 ml-2">(Read: {{ $notification->read_at->diffForHumans() }})</span>
                                    @else
                                        <span class="text-blue-600 ml-2">(Unread)</span>
                                    @endif
                                </p>

                                @if(isset($notification->data['job_id']) && Route::has('jobs.show'))
                                    <div class="mt-2">
                                        <a href="{{ route('jobs.show', $notification->data['job_id']) }}" class="text-sm text-blue-600 hover:underline font-medium">
                                            View Job Details &raquo;
                                        </a>
                                    </div>
                                @endif
                                @if(isset($notification->data['student_email'])) {{-- For admin notification --}}
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-600">
                                            Student: {{ $notification->data['student_name'] ?? '' }}
                                            ({{ $notification->data['student_email'] }})
                                        </p>
                                        @if(Route::has('admin.users.index'))
                                            <a href="{{ route('admin.users.index', ['email' => $notification->data['student_email']]) }}"
                                               class="text-sm text-blue-600 hover:underline font-medium">
                                                View User in Admin &raquo;
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            @if(is_null($notification->read_at))
                                <form action="{{ route('notifications.read', $notification) }}" method="POST" class="ml-4 flex-shrink-0">
                                    @csrf
                                    <button type="submit"
                                            class="text-xs bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-1 px-3 rounded-lg shadow-sm transition duration-150">
                                        Mark as read
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
