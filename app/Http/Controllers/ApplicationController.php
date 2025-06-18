<?php

namespace App\Http\Controllers;

use App\Models\Job; // Added
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Added for web auth
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage; // Ensured present

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
        // Logic to be detailed separately
        // This subtask is only to ensure the method structure is added.
    }
}
