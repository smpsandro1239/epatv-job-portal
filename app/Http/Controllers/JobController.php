<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\AreaOfInterest;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = Job::query()->with(['areaOfInterest', 'company']);

        // Filter by Area of Interest (Category)
        if ($request->filled('area_of_interest_id')) {
            $query->where('area_of_interest_id', $request->input('area_of_interest_id'));
        }

        // Filter by Location
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->input('location') . '%');
        }

        // Filter by Contract Type
        if ($request->filled('contract_type')) {
            $query->where('contract_type', $request->input('contract_type'));
        }

        $jobs = $query->latest()->paginate(10);

        $areasOfInterest = AreaOfInterest::all();

        // Get distinct locations, filtering out null or empty strings
        $locations = Job::distinct()
                        ->whereNotNull('location')
                        ->where('location', '<>', '')
                        ->orderBy('location')
                        ->pluck('location');

        // Get distinct contract types, filtering out null or empty strings
        $contractTypes = Job::distinct()
                            ->whereNotNull('contract_type')
                            ->where('contract_type', '<>', '')
                            ->orderBy('contract_type')
                            ->pluck('contract_type');

        return view('jobs.index', [
            'jobs' => $jobs,
            'areasOfInterest' => $areasOfInterest,
            'locations' => $locations,
            'contractTypes' => $contractTypes,
            'filters' => $request->all(), // Pass all current request parameters to the view for filter persistence
        ]);
    }

    /**
     * Display the specified job.
     *
     * @param  \App\Models\Job  $job
     * @return \Illuminate\View\View
     */
    public function show(Job $job)
    {
        $job->load(['company' => function ($query) {
            // Select specific fields from the User model representing the company
            $query->select('id', 'name', 'company_name', 'company_logo', 'company_website', 'company_description');
        }, 'areaOfInterest']);

        // For "Save Job" button state if student is logged in
        $isSaved = false;
        if (Auth::check() && Auth::user()->role === 'student') {
            $isSaved = Auth::user()->savedJobs()->where('job_id', $job->id)->exists();
        }

        return view('jobs.show', compact('job', 'isSaved'));
    }
}
