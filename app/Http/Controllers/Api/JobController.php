<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job; // Assuming Job model maps to jobs_employment table
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB; // Required for now() typically, though Carbon is preferred

class JobController extends Controller
{
    /**
     * Get the count of active jobs.
     *
     * An active job is one that has not passed its expiration_date or has a null expiration_date.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function activeCount(): JsonResponse
    {
        // Using Carbon\Carbon::now() is generally preferred over DB::raw('now()') for testability and flexibility
        $now = \Carbon\Carbon::now();

        $count = Job::where(function ($query) use ($now) {
            $query->where('expiration_date', '>', $now)
                  ->orWhereNull('expiration_date');
        })->count();

        return response()->json(['active_job_count' => $count]);
    }
}
