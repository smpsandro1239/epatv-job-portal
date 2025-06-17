<?php

namespace App\Http\Controllers;

use App\Models\AreaOfInterest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator; // Added for Validator

class StudentController extends Controller
{
    /**
     * Display the authenticated student's profile.
     */
    public function show()
    {
        $user = Auth::user();
        if ($user->role !== 'student') {
            abort(403, 'Unauthorized action.');
        }
        $user->load('areasOfInterest');
        return view('student.profile.show', compact('user'));
    }

    /**
     * Show the form for editing the authenticated student's profile.
     */
    public function edit()
    {
        $user = Auth::user();
        if ($user->role !== 'student') {
            abort(403, 'Unauthorized action.');
        }
        $user->load('areasOfInterest');
        $allAreasOfInterest = AreaOfInterest::all();
        return view('student.profile.edit', compact('user', 'allAreasOfInterest'));
    }

    /**
     * Update the authenticated student's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'student') {
            abort(403, 'Unauthorized action.');
        }

        $currentYear = date('Y');
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|nullable|string|max:20',
            'course_completion_year' => 'sometimes|nullable|integer|min:1980|max:' . ($currentYear + 5),
            'photo' => 'sometimes|nullable|image|max:2048', // Max 2MB
            'cv' => 'sometimes|nullable|mimes:pdf,doc,docx|max:2048', // Max 2MB
            'areas_of_interest' => 'sometimes|nullable|array',
            'areas_of_interest.*' => 'integer|exists:areas_of_interest,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('student.profile.edit')
                        ->withErrors($validator)
                        ->withInput();
        }

        // Update basic fields
        if ($request->filled('name')) {
            $user->name = $request->input('name');
        }
        if ($request->filled('phone')) {
            $user->phone = $request->input('phone');
        }
        if ($request->filled('course_completion_year')) {
            $user->course_completion_year = $request->input('course_completion_year');
        }

        // Handle Photo Upload
        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::delete($user->photo);
            }
            $path = $request->file('photo')->store('public/user_photos');
            $user->photo = $path;
        }

        // Handle CV Upload
        if ($request->hasFile('cv')) {
            if ($user->cv) {
                Storage::delete($user->cv);
            }
            $path = $request->file('cv')->store('public/user_cvs');
            $user->cv = $path;
        }

        $user->save();

        // Sync Areas of Interest
        if ($request->has('areas_of_interest')) {
            $user->areasOfInterest()->sync($request->input('areas_of_interest', []));
        } else {
            // If areas_of_interest is not present or empty, detach all
            $user->areasOfInterest()->detach();
        }

        return redirect()->route('student.profile.show')->with('success', 'Profile updated successfully.');
    }

    /**
     * Display a list of the authenticated student's job applications.
     */
    public function listApplications()
    {
        $user = Auth::user();
        if ($user->role !== 'student') {
            abort(403, 'Unauthorized action.');
        }

        $applications = $user->applications()
                              ->with(['job' => function ($query) {
                                  $query->select('id', 'title', 'company_id', 'location'); // Added location
                              }, 'job.company' => function ($query) {
                                  $query->select('id', 'name');
                              }])
                              ->latest()
                              ->paginate(10); // Paginate for web view

        return view('student.applications.index', compact('applications'));
    }
}
