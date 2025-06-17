<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    /**
     * Display the authenticated student's profile.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        $user = Auth::user(); // Correct way to get authenticated user with default guard 'api'

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Ensure the user has the 'student' role.
        // This will be primarily handled by middleware, but an extra check doesn't hurt.
        if ($user->role !== 'student') {
            return response()->json(['message' => 'Forbidden: User is not a student'], 403);
        }

        $user->load('areasOfInterest');

        // Prepare data for response
        $profileData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'course_completion_year' => $user->course_completion_year,
            'areas_of_interest' => $user->areasOfInterest->map(function ($area) {
                return [
                    'id' => $area->id,
                    'name' => $area->name,
                ];
            }),
            'photo_url' => $user->photo ? Storage::url($user->photo) : null,
            'cv_url' => $user->cv ? Storage::url($user->cv) : null,
            // Additional fields if any from User model
            'registration_status' => $user->registration_status,
            'email_verified_at' => $user->email_verified_at,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];

        return response()->json($profileData);
    }

    /**
     * Update the authenticated student's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($user->role !== 'student') {
            return response()->json(['message' => 'Forbidden: User is not a student'], 403);
        }

        $currentYear = date('Y');
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'course_completion_year' => 'sometimes|integer|min:1980|max:' . ($currentYear + 5), // Max current year + 5
            'photo' => 'sometimes|file|image|max:2048', // Max 2MB
            'cv' => 'sometimes|file|mimes:pdf,doc,docx|max:2048', // Max 2MB
            'areas_of_interest' => 'sometimes|array',
            'areas_of_interest.*' => 'integer|exists:areas_of_interest,id', // Removed sometimes
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update basic fields
        if ($request->has('name')) {
            $user->name = $request->input('name');
        }
        if ($request->has('phone')) {
            $user->phone = $request->input('phone');
        }
        if ($request->has('course_completion_year')) {
            $user->course_completion_year = $request->input('course_completion_year');
        }

        // Handle Photo Upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo) {
                Storage::delete($user->photo);
            }
            $path = $request->file('photo')->store('public/user_photos'); // Store in 'storage/app/public/user_photos'
            $user->photo = $path; // Save path relative to 'storage/app'
        }

        // Handle CV Upload
        if ($request->hasFile('cv')) {
            // Delete old CV if exists
            if ($user->cv) {
                Storage::delete($user->cv);
            }
            $path = $request->file('cv')->store('public/user_cvs'); // Store in 'storage/app/public/user_cvs'
            $user->cv = $path; // Save path relative to 'storage/app'
        }

        $user->save();

        // Sync Areas of Interest
        if ($request->has('areas_of_interest')) {
            $user->areasOfInterest()->sync($request->input('areas_of_interest'));
        }

        $user->refresh(); // Refresh model to get updated relations and attributes
        $user->load('areasOfInterest'); // Ensure areasOfInterest are loaded for the response

        // Prepare data for response (similar to show method)
        $profileData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'course_completion_year' => $user->course_completion_year,
            'areas_of_interest' => $user->areasOfInterest->map(function ($area) {
                return [
                    'id' => $area->id,
                    'name' => $area->name,
                ];
            }),
            'photo_url' => $user->photo ? Storage::url($user->photo) : null,
            'cv_url' => $user->cv ? Storage::url($user->cv) : null,
            'registration_status' => $user->registration_status,
            'email_verified_at' => $user->email_verified_at,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];

        return response()->json(['message' => 'Profile updated successfully', 'data' => $profileData]);
    }

    /**
     * Toggle the saved status of a job for the authenticated student.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $jobId
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleSaveJob(Request $request, $jobId)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'student') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            // Ensure the job exists
            \App\Models\Job::findOrFail($jobId);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Job not found'], 404);
        }

        // Toggle the job in the saved_jobs pivot table
        $user->savedJobs()->toggle($jobId);

        // Check the current saved state
        $isSaved = $user->savedJobs()->where('job_id', $jobId)->exists();

        $message = $isSaved ? 'Job saved successfully.' : 'Job unsaved successfully.';

        return response()->json([
            'message' => $message,
            'is_saved' => $isSaved,
        ]);
    }

    /**
     * Get the paginated list of job applications for the authenticated student.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getApplications(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'student') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $applications = $user->applications()
                              ->with(['job' => function ($query) {
                                  $query->select('id', 'title', 'company_id'); // Select only necessary fields from job
                              }, 'job.company' => function ($query) {
                                  $query->select('id', 'name'); // Select only necessary fields from company (user)
                              }])
                              ->latest()
                              ->paginate(15); // Paginate with 15 items per page, or use config

        return response()->json($applications);
    }
}
