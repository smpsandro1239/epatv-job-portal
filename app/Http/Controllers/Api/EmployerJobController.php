<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\User; // Added
use App\Models\Notification as DbNotification; // Added
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EmployerJobController extends Controller
{
    /**
     * Display a listing of the employer's jobs.
     */
    public function index(Request $request)
    {
        $employer = Auth::user();
        $jobs = Job::where('company_id', $employer->id)
                    ->with('areaOfInterest') // Eager load area of interest
                    ->latest()
                    ->paginate(15); // Or a configurable number

        return response()->json($jobs);
    }

    /**
     * Store a newly created job in storage.
     */
    public function store(Request $request)
    {
        $employer = Auth::user();

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'area_of_interest_id' => 'required|integer|exists:areas_of_interest,id',
            'description' => 'required|string',
            'location' => 'nullable|string|max:255',
            'contract_type' => 'nullable|string|max:255',
            'salary' => 'nullable|string|max:255',
            'expiration_date' => 'nullable|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $job = new Job($request->all());
        $job->company_id = $employer->id;
        // Assuming 'posted_by' should also be the employer for their own job posts.
        // If 'posted_by' has a different meaning (e.g., an admin posting on behalf of company), this might change.
        // For now, setting both to the employer.
        $job->posted_by = $employer->id;
        $job->save();

        $job->load('areaOfInterest'); // Load for the response

        // --- Start Notification Logic ---
        $jobAreaOfInterestId = $job->area_of_interest_id;
        if ($jobAreaOfInterestId && $job->areaOfInterest) { // Ensure area of interest is set and loaded
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

        return response()->json($job, 201);
    }

    /**
     * Display the specified job.
     */
    public function show(Job $job)
    {
        $employer = Auth::user();
        if ($job->company_id !== $employer->id) {
            return response()->json(['message' => 'Forbidden. You do not own this job listing.'], 403);
        }
        $job->load('areaOfInterest');
        return response()->json($job);
    }

    /**
     * Update the specified job in storage.
     */
    public function update(Request $request, Job $job)
    {
        $employer = Auth::user();
        if ($job->company_id !== $employer->id) {
            return response()->json(['message' => 'Forbidden. You do not own this job listing.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'area_of_interest_id' => 'sometimes|required|integer|exists:areas_of_interest,id',
            'description' => 'sometimes|required|string',
            'location' => 'sometimes|nullable|string|max:255',
            'contract_type' => 'sometimes|nullable|string|max:255',
            'salary' => 'sometimes|nullable|string|max:255',
            'expiration_date' => 'sometimes|nullable|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $job->fill($request->all());
        $job->save();

        $job->load('areaOfInterest');

        return response()->json($job);
    }

    /**
     * Remove the specified job from storage.
     */
    public function destroy(Job $job)
    {
        $employer = Auth::user();
        if ($job->company_id !== $employer->id) {
            return response()->json(['message' => 'Forbidden. You do not own this job listing.'], 403);
        }

        $job->delete();

        return response()->json(['message' => 'Job deleted successfully.'], 200);
        // Or return response(null, 204); for No Content
    }
}
