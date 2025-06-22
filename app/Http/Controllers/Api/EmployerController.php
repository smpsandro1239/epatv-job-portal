<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User; // Though Auth::user() will be used primarily
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EmployerController extends Controller
{
    /**
     * Display the authenticated employer's profile.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show()
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['employer', 'admin', 'superadmin'])) {
             // Allowing admin/superadmin to view might be useful, but strictly 'employer' for now as per role middleware target.
             // For this specific route, middleware 'role:employer' will be the primary guard.
            return response()->json(['message' => 'Unauthorized. User is not an employer.'], 403);
        }

        $profileData = $this->formatProfileData($user);
        return response()->json($profileData);
    }

    /**
     * Update the authenticated employer's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['employer', 'admin', 'superadmin'])) {
            // Middleware 'role:employer' is the primary guard.
            return response()->json(['message' => 'Unauthorized. User is not an employer.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255', // Contact person name
            'company_name' => 'sometimes|string|max:255',
            'company_city' => 'sometimes|nullable|string|max:255',
            'company_website' => 'sometimes|nullable|url|max:255',
            'company_description' => 'sometimes|nullable|string',
            'company_logo' => 'sometimes|nullable|file|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Max 2MB
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update basic fields
        if ($request->has('name')) {
            $user->name = $request->input('name');
        }
        if ($request->has('company_name')) {
            $user->company_name = $request->input('company_name');
        }
        // Use filled() for nullable fields to allow sending empty string to nullify
        if ($request->filled('company_city')) {
            $user->company_city = $request->input('company_city');
        } elseif ($request->exists('company_city')) { // If key exists but is empty (e.g. ""), set to null
             $user->company_city = null;
        }

        if ($request->filled('company_website')) {
            $user->company_website = $request->input('company_website');
        } elseif ($request->exists('company_website')) {
             $user->company_website = null;
        }

        if ($request->filled('company_description')) {
            $user->company_description = $request->input('company_description');
        } elseif ($request->exists('company_description')) {
             $user->company_description = null;
        }


        // Handle Company Logo Upload
        if ($request->hasFile('company_logo')) {
            if ($user->company_logo) {
                Storage::disk('public')->delete($user->company_logo);
            }
            $path = $request->file('company_logo')->store('company_logos', 'public');
            $user->company_logo = $path;
        } elseif ($request->exists('company_logo') && is_null($request->input('company_logo'))) {
            // If 'company_logo' is explicitly sent as null (e.g. to remove logo without uploading new one)
            if ($user->company_logo) {
                Storage::disk('public')->delete($user->company_logo);
                $user->company_logo = null;
            }
        }

        $user->save();

        $updatedProfileData = $this->formatProfileData($user);
        return response()->json(['message' => 'Employer profile updated successfully', 'data' => $updatedProfileData]);
    }

    /**
     * Format user profile data for consistent response.
     *
     * @param \App\Models\User $user
     * @return array
     */
    private function formatProfileData(User $user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name, // Contact person name
            'email' => $user->email,
            'role' => $user->role,
            'company_name' => $user->company_name,
            'company_city' => $user->company_city,
            'company_website' => $user->company_website,
            'company_description' => $user->company_description,
            'company_logo_url' => $user->company_logo ? Storage::url($user->company_logo) : null,
            'registration_status' => $user->registration_status,
            'email_verified_at' => $user->email_verified_at,
        ];
    }

    /**
     * List all applications for jobs posted by the authenticated employer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listApplications(Request $request)
    {
        $employer = Auth::user();

        if (!$employer || !in_array($employer->role, ['employer', 'admin', 'superadmin'])) {
            // Middleware 'role:employer' is the primary guard.
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Get IDs of jobs posted by the employer
        $jobIds = $employer->postedJobs()->pluck('id');

        // Fetch applications for those jobs
        $applications = \App\Models\Application::whereIn('job_id', $jobIds)
            ->with([
                'user' => function ($query) { // Applicant details
                    $query->select('id', 'name', 'email', 'phone'); // Select specific fields
                },
                'job' => function ($query) { // Job details
                    $query->select('id', 'title', 'location'); // Select specific fields
                }
            ])
            ->latest() // Order by latest applications first
            ->paginate(15); // Or a configurable number

        return response()->json($applications);
    }
}
