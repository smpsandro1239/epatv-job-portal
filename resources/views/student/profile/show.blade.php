@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Student Profile</h1>

    <div>
        <strong>Name:</strong> {{ $user->name }}
    </div>
    <div>
        <strong>Email:</strong> {{ $user->email }}
    </div>
    <div>
        <strong>Phone:</strong> {{ $user->phone ?? 'Not provided' }}
    </div>
    <div>
        <strong>Course Completion Year:</strong> {{ $user->course_completion_year ?? 'Not provided' }}
    </div>

    @if($user->photo)
    <div>
        <strong>Photo:</strong><br>
        <img src="{{ Storage::url($user->photo) }}" alt="User Photo" style="max-width: 200px; max-height: 200px;">
    </div>
    @endif

    @if($user->cv)
    <div>
        <strong>CV:</strong><br>
        <a href="{{ Storage::url($user->cv) }}" target="_blank">Download CV</a>
    </div>
    @endif

    <div>
        <strong>Areas of Interest:</strong>
        @if($user->areasOfInterest->isNotEmpty())
            <ul>
                @foreach($user->areasOfInterest as $area)
                    <li>{{ $area->name }}</li>
                @endforeach
            </ul>
        @else
            <p>No areas of interest specified.</p>
        @endif
    </div>

    <a href="{{ route('student.profile.edit') }}">Edit Profile</a>
</div>
@endsection
