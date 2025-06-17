<?php

namespace App\Http\Controllers;

use App\Models\User; // Not strictly needed if only Auth::user() is used
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EmployerController extends Controller
{
    /**
     * Display the authenticated employer's profile.
     */
    public function show()
    {
        $user = Auth::user();
        // Middleware 'role:employer' should handle role check.
        // Add additional check if necessary: if ($user->role !== 'employer') abort(403);
        return view('employer.profile.show', compact('user'));
    }

    /**
     * Show the form for editing the authenticated employer's profile.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('employer.profile.edit', compact('user'));
    }

    /**
     * Update the authenticated employer's profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255', // Contact person name
            'company_name' => 'sometimes|string|max:255',
            'company_city' => 'sometimes|nullable|string|max:255',
            'company_website' => 'sometimes|nullable|url|max:255',
            'company_description' => 'sometimes|nullable|string',
            'company_logo' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Max 2MB
        ]);

        if ($validator->fails()) {
            return redirect()->route('employer.profile.edit')
                        ->withErrors($validator)
                        ->withInput();
        }

        // Update basic fields
        if ($request->filled('name')) {
            $user->name = $request->input('name');
        }
        if ($request->filled('company_name')) {
            $user->company_name = $request->input('company_name');
        }

        // Handle nullable fields allowing them to be cleared
        $user->company_city = $request->filled('company_city') ? $request->input('company_city') : null;
        $user->company_website = $request->filled('company_website') ? $request->input('company_website') : null;
        $user->company_description = $request->filled('company_description') ? $request->input('company_description') : null;

        // Handle Company Logo Upload
        if ($request->hasFile('company_logo')) {
            if ($user->company_logo) {
                Storage::delete($user->company_logo);
            }
            $path = $request->file('company_logo')->store('public/company_logos');
            $user->company_logo = $path;
        } elseif ($request->exists('remove_company_logo') && $request->input('remove_company_logo')) {
            // If a checkbox like 'remove_company_logo' is checked
            if ($user->company_logo) {
                Storage::delete($user->company_logo);
                $user->company_logo = null;
            }
        }

        $user->save();

        return redirect()->route('employer.profile.show')->with('success', 'Employer profile updated successfully.');
    }

    /**
     * List all applications for jobs posted by the authenticated employer - Web version.
     */
    public function listApplications(Request $request)
    {
        $employer = Auth::user();
        // Middleware 'role:employer' handles role check.

        $jobIds = $employer->postedJobs()->pluck('id');
        $applicationsQuery = \App\Models\Application::whereIn('job_id', $jobIds);

        // Filter by Job
        if ($request->filled('filter_job_id')) {
            $applicationsQuery->where('job_id', $request->input('filter_job_id'));
        }

        // Filter by Status
        if ($request->filled('filter_status')) {
            $applicationsQuery->where('status', $request->input('filter_status'));
        }

        $applications = $applicationsQuery->with([
            'user' => function ($query) { // Applicant details
                $query->select('id', 'name', 'email', 'phone', 'cv');
            },
            'job' => function ($query) { // Job details
                $query->select('id', 'title', 'location');
            }
        ])
        ->latest() // Order by latest applications first
        ->paginate(10); // Paginate for web view

        // For filter dropdowns
        $employerJobs = $employer->postedJobs()->select('id', 'title')->orderBy('title')->get();
        // Using a fixed list of statuses for the filter, can be dynamic if needed
        $statuses = ['pending', 'reviewed', 'shortlisted', 'hired', 'rejected'];

        return view('employer.applications.index', [
            'applications' => $applications,
            'employerJobs' => $employerJobs,
            'statuses' => $statuses,
            'filters' => $request->only(['filter_job_id', 'filter_status']),
        ]);
    }
}
