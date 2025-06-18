<?php

namespace App\Http\Controllers;

use App\Models\Job; // Added
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Added for web auth
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage; // Ensured present
use Illuminate\Support\Str; // Added for Str::slug

class ApplicationController extends Controller
{
    public function create(Job $job)
    {
        $applicant = Auth::user(); // Uses web auth

        if (!$applicant || $applicant->role !== 'student') {
            // Or handle via middleware more broadly
            return redirect()->route('login')->with('error', 'You must be logged in as a student to apply.');
        }

        $hasApplied = Application::where('user_id', $applicant->id)
                                ->where('job_id', $job->id)
                                ->exists();

        if ($hasApplied) {
            return redirect()->route('jobs.show', $job)
                             ->with('error', 'You have already applied for this job.');
        }

        return view('jobs.apply', [
            'job' => $job,
            'applicant' => $applicant
        ]);
    }

    public function apply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_id' => 'required|exists:jobs_employment,id',
            'cover_letter' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $application = Application::create([
            'user_id' => auth('api')->id(),
            'job_id' => $request->job_id,
            'status' => 'pending',
            'cover_letter' => $request->cover_letter,
        ]);
        return response()->json([
            'message' => 'Application submitted successfully',
            'application' => $application
        ], 201);
    }

    public function store(Request $request, Job $job)
    {
        $applicant = Auth::user(); // Uses web auth

        if (!$applicant || $applicant->role !== 'student') {
            // This should ideally be caught by middleware, but an extra check is fine.
            return redirect()->route('login')->with('error', 'You must be logged in as a student to apply.');
        }

        // Optional: Double check if student has already applied (race condition or client-side bypass)
        $hasApplied = Application::where('user_id', $applicant->id)
                                ->where('job_id', $job->id)
                                ->exists();
        if ($hasApplied) {
            // This check might be better handled by a unique constraint in the DB
            // or by disabling the apply button client-side after first click.
            return redirect()->route('jobs.show', $job)
                             ->with('error', 'You have already applied for this job.');
        }

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'course_completion_year' => ['nullable', 'integer', 'min:1980', 'max:' . (date('Y') + 7)],
            'cv_path' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:2048'], // Validates the uploaded file named 'cv_path'
            'message' => ['nullable', 'string', 'max:5000'],
        ]);

        // CV File Handling Block STARTS HERE
        $cvPathToStoreInDb = null;
        if ($request->hasFile('cv_path')) {
            $file = $request->file('cv_path');
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = Str::slug($originalFilename); // Illuminate\Support\Str
            $extension = $file->getClientOriginalExtension();
            $filenameToStore = $safeFilename . '_' . time() . '.' . $extension;
            // $applicant variable should be defined from Auth::user() at the beginning of the store method
            $cvPathToStoreInDb = $file->storeAs("public/application_cvs/{$applicant->id}/{$job->id}", $filenameToStore);
        }
        // CV File Handling Block ENDS HERE

        // Application Record Creation Block STARTS HERE
        Application::create([
            'user_id' => $applicant->id, // $applicant is Auth::user()
            'job_id' => $job->id,
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'course_completion_year' => $validatedData['course_completion_year'],
            'cv_path' => $cvPathToStoreInDb, // Path from CV file handling step
            'message' => $validatedData['message'], // This field was renamed from cover_letter
            'status' => 'pending', // Default status
        ]);
        // Application Record Creation Block ENDS HERE

        return redirect()->route('student.applications.index')
                         ->with('success', 'Your application for "' . $job->title . '" has been submitted successfully!');
    }
}
