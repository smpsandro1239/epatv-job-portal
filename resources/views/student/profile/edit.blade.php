@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Student Profile</h1>

    @if ($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('student.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}">
        </div>

        <div>
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
        </div>

        <div>
            <label for="course_completion_year">Course Completion Year:</label>
            <input type="number" id="course_completion_year" name="course_completion_year" value="{{ old('course_completion_year', $user->course_completion_year) }}">
        </div>

        <div>
            <label for="photo">Photo (Max 2MB):</label>
            <input type="file" id="photo" name="photo">
            @if($user->photo)
                <p>Current photo:</p>
                <img src="{{ Storage::url($user->photo) }}" alt="User Photo" style="max-width: 100px; max-height: 100px;">
            @endif
        </div>

        <div>
            <label for="cv">CV (PDF, DOC, DOCX - Max 2MB):</label>
            <input type="file" id="cv" name="cv">
            @if($user->cv)
                <p>Current CV: <a href="{{ Storage::url($user->cv) }}" target="_blank">Download</a></p>
            @endif
        </div>

        <div>
            <label for="areas_of_interest">Areas of Interest:</label>
            <select name="areas_of_interest[]" id="areas_of_interest" multiple>
                @foreach($allAreasOfInterest as $area)
                    <option value="{{ $area->id }}"
                        {{ (in_array($area->id, old('areas_of_interest', $user->areasOfInterest->pluck('id')->toArray())) ? 'selected' : '') }}>
                        {{ $area->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit">Update Profile</button>
    </form>
</div>
@endsection
