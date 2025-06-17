<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Job;
use App\Models\Application; // Added
use App\Models\AreaOfInterest; // Added
use App\Models\RegistrationWindow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Added
use Illuminate\Support\Facades\Notification; // For future notifications
use App\Notifications\RegistrationApprovedNotification; // For future notifications

class AdminController extends Controller
{
    public function dashboard()
    {
        // Ensure only superadmin can access these detailed stats
        if (Auth::user()->role !== 'superadmin') {
            // This check is an additional safeguard if the route accidentally allows other admin roles.
            // The route should ideally be specific to 'superadmin'.
            return response()->json(['message' => 'Unauthorized. Superadmin access required.'], 403);
        }

        $stats = [
            'total_users' => User::where('role', '!=', 'superadmin')->count(), // Exclude superadmin from total users
            'total_jobs' => Job::count(),
            'pending_registrations' => User::where('registration_status', 'pending')->where('role', '!=', 'superadmin')->count(),

            // User Role Counts (excluding superadmin)
            'students_count' => User::whereIn('role', ['candidate', 'student'])->count(),
            'employers_count' => User::whereIn('role', ['employer', 'admin'])->where('role', '!=', 'superadmin')->count(),

            // CV Uploads for students
            'students_with_cv_count' => User::whereIn('role', ['candidate', 'student'])->whereNotNull('cv')->count(),

            // Job Postings by Location (Top 5 for brevity, or all if preferred)
            'jobs_by_location' => Job::select('location', DB::raw('count(*) as total'))
                                     ->whereNotNull('location')->where('location', '!=', '')
                                     ->groupBy('location')->orderBy('total', 'desc')->take(5)->get(),

            // Job Postings by Area of Interest (Category)
            'jobs_by_area' => Job::join('areas_of_interest', 'jobs_employment.area_of_interest_id', '=', 'areas_of_interest.id')
                                 ->select('areas_of_interest.name as area_name', DB::raw('count(jobs_employment.id) as total'))
                                 ->groupBy('areas_of_interest.name')->orderBy('total', 'desc')->take(5)->get(),

            // Job Postings by Month
            'jobs_by_month' => Job::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as total'))
                                  ->groupBy('month')->orderBy('month', 'asc')->get(),

            // Job Postings by Contract Type
            'jobs_by_contract_type' => Job::select('contract_type', DB::raw('count(*) as total'))
                                         ->whereNotNull('contract_type')->where('contract_type', '!=', '')
                                         ->groupBy('contract_type')->orderBy('total', 'desc')->take(5)->get(),

            'total_applications' => Application::count(),
        ];
        return response()->json($stats);
    }

    /**
     * Display a listing of users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('registration_status')) {
            $query->where('registration_status', $request->input('registration_status'));
        }

        // Exclude superadmin from the list by default, unless explicitly requested
        if ($request->input('role') !== 'superadmin') {
            $query->where('role', '!=', 'superadmin');
        }

        $users = $query->latest()->paginate(15); // Paginate results

        return response()->json($users);
    }

    /**
     * Approve a user's registration.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function approveRegistration(Request $request, User $user)
    {
        // Only pending users can be approved by this action
        if ($user->registration_status !== 'pending') {
            return response()->json(['message' => 'User is not pending approval or already processed.'], 400);
        }

        $user->registration_status = 'approved';
        // Optionally, verify email if this step implies it for pending users
        // if (!$user->hasVerifiedEmail()) {
        //     $user->email_verified_at = now();
        // }
        $user->save();

        // TODO: Send RegistrationApprovedNotification in a later step
        // Notification::send($user, new RegistrationApprovedNotification());

        return response()->json(['message' => 'User registration approved successfully.', 'user' => $user]);
    }

    /**
     * Get the current registration window settings.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRegistrationWindow()
    {
        // Assuming there's only one primary registration window record, or a specific one to manage.
        // Using firstOrCreate to ensure a record exists if the table is empty.
        // Default values here are minimal; adjust as needed if creating for the first time.
        $window = RegistrationWindow::firstOrCreate([], [
            'start_time' => now()->addDay(),
            'end_time' => now()->addDays(2),
            'max_registrations' => 0,
            'is_active' => false,
            'password' => null, // No default password
            'current_registrations' => 0,
            'first_use_time' => null,
        ]);
        return response()->json($window);
    }

    /**
     * Update the registration window settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateRegistrationWindow(Request $request)
    {
        $window = RegistrationWindow::firstOrCreate([], [ /* defaults if creating */ ]);

        $validator = Validator::make($request->all(), [
            'start_time' => 'sometimes|required|date_format:Y-m-d H:i:s',
            'end_time' => 'sometimes|required|date_format:Y-m-d H:i:s|after:start_time',
            'max_registrations' => 'sometimes|required|integer|min:0',
            'password' => 'sometimes|nullable|string|min:6|max:255', // Add min/max if desired
            'is_active' => 'sometimes|required|boolean',
            // current_registrations and first_use_time are typically not directly updatable by admin this way
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $updateData = $request->only(['start_time', 'end_time', 'max_registrations', 'is_active']);

        // Handle password update - only if a new password is provided
        if ($request->filled('password')) {
            $updateData['password'] = \Illuminate\Support\Facades\Hash::make($request->input('password'));
            // When password changes, reset first_use_time and current_registrations as per README logic
            $updateData['first_use_time'] = null;
            $updateData['current_registrations'] = 0;
        } elseif ($request->exists('password') && is_null($request->input('password'))) {
            // If password is explicitly set to null (to remove it)
            $updateData['password'] = null;
            $updateData['first_use_time'] = null; // Also reset dependent fields
            $updateData['current_registrations'] = 0;
        }


        // If activating, and other windows exist that are active, deactivate them.
        // This assumes a single active window at a time.
        if (isset($updateData['is_active']) && $updateData['is_active']) {
            RegistrationWindow::where('id', '!=', $window->id)->update(['is_active' => false]);
        }

        // If a significant change like start_time, end_time, or max_registrations occurs,
        // admin might want to reset current_registrations and first_use_time.
        // This logic can be more nuanced. For simplicity, only password change resets them now.
        // Example: if is_active is toggled to false, maybe reset current_registrations?
        if (isset($updateData['is_active']) && !$updateData['is_active']) {
             // $updateData['current_registrations'] = 0; // Optional: reset if deactivated
             // $updateData['first_use_time'] = null; // Optional
        }


        $window->update($updateData);

        return response()->json(['message' => 'Registration window updated successfully.', 'window' => $window]);
    }
}
