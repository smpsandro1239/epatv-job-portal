<?php

namespace App\Http\Controllers; // Web Controller Namespace

use App\Models\Job;
use App\Models\AreaOfInterest;
use App\Models\User; // Added
use App\Models\Notification as DbNotification; // Added
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator; // Not strictly needed if using $request->validate()

class EmployerJobController extends Controller
{
    /**
     * Display a listing of the employer's jobs.
     */
    public function index(Request $request)
    {
        $employer = Auth::user();
        $jobs = Job::where('company_id', $employer->id)
                    ->with('areaOfInterest')
                    ->withCount('applications') // Added application count
                    ->latest()
                    ->paginate(10); // Paginate for web view

        return view('employer.jobs.index', compact('jobs'));
    }

    /**
     * Show the form for creating a new job.
     */
    public function create()
    {
        $areasOfInterest = AreaOfInterest::orderBy('name')->get();
        return view('employer.jobs.create', compact('areasOfInterest'));
    }

    /**
     * Store a newly created job in storage.
     */
    public function store(Request $request)
    {
        $employer = Auth::user();

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'area_of_interest_id' => 'required|integer|exists:areas_of_interest,id',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'contract_type' => 'nullable|string|max:255',
            'salary' => 'nullable|string|max:255',
            'expiration_date' => 'nullable|date|after_or_equal:today',
        ]);

        $job = new Job($validatedData);
        $job->company_id = $employer->id;
        $job->posted_by = $employer->id; // Consistent with API controller
        $job->save();

        // --- Start Notification Logic ---
        // Ensure areaOfInterest is loaded for the notification message
        $job->load('areaOfInterest');
        $jobAreaOfInterestId = $job->area_of_interest_id;

        if ($jobAreaOfInterestId && $job->areaOfInterest) {
            $studentsToNotify = User::whereIn('role', ['candidate', 'student'])
                ->whereHas('areasOfInterest', function ($query) use ($jobAreaOfInterestId) {
                    $query->where('areas_of_interest.id', $jobAreaOfInterestId);
                })->get();

            foreach ($studentsToNotify as $student) {
                DbNotification::create([
                    'user_id' => $student->id,
                    'type' => 'NewJobInAreaOfInterestNotification',
                    'data' => [
                        'message' => "A new job '{$job->title}' has been posted in your area of interest: {$job->areaOfInterest->name}.",
                        'job_id' => $job->id,
                        'job_title' => $job->title,
                        'area_name' => $job->areaOfInterest->name,
                    ]
                ]);
            }
        }
        // --- End Notification Logic ---

        return redirect()->route('employer.jobs.index')->with('success', 'Job created successfully.');
    }

    /**
     * Display the specified resource.
     * (Often not used directly if edit is the primary interaction for owned resources)
     */
    public function show(Job $job)
    {
        $employer = Auth::user();
        if ($job->company_id !== $employer->id) {
            abort(403, 'Unauthorized action.');
        }
        $job->load(['areaOfInterest', 'company', 'postedBy'])->loadCount('applications'); // Eager load for display & count
        return view('employer.jobs.show', compact('job'));
    }


    /**
     * Show the form for editing the specified job.
     */
    public function edit(Job $job)
    {
        $employer = Auth::user();
        if ($job->company_id !== $employer->id) {
            abort(403, 'Unauthorized action.');
        }
        $areasOfInterest = AreaOfInterest::orderBy('name')->get();
        return view('employer.jobs.edit', compact('job', 'areasOfInterest'));
    }

    /**
     * Update the specified job in storage.
     */
    public function update(Request $request, Job $job)
    {
        $employer = Auth::user();
        if ($job->company_id !== $employer->id) {
            abort(403, 'Unauthorized action.');
        }

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'area_of_interest_id' => 'required|integer|exists:areas_of_interest,id',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'contract_type' => 'nullable|string|max:255',
            'salary' => 'nullable|string|max:255',
            'expiration_date' => 'nullable|date|after_or_equal:today',
        ]);

        $job->fill($validatedData);
        $job->save();

        return redirect()->route('employer.jobs.index')->with('success', 'Job updated successfully.');
    }

    /**
     * Remove the specified job from storage.
     */
    public function destroy(Job $job)
    {
        $employer = Auth::user();
        if ($job->company_id !== $employer->id) {
            abort(403, 'Unauthorized action.');
        }
        $job->delete();
        return redirect()->route('employer.jobs.index')->with('success', 'Job deleted successfully.');
    }
}
