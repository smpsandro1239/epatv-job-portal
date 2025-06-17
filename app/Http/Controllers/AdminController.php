<?php

namespace App\Http\Controllers; // Web Controller Namespace

use App\Models\User;
use App\Models\RegistrationWindow;
use App\Models\Job;
use App\Models\Application;
use App\Models\AreaOfInterest;
use App\Models\Notification as DbNotification; // Added
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Display a listing of users for the admin panel.
     */
    public function listUsers(Request $request)
    {
        // Ensure the authenticated user is a superadmin (though middleware should also enforce this)
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Unauthorized action.');
        }

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

        $users = $query->orderBy('created_at', 'desc')->paginate(15); // Paginate results

        // For filter dropdowns - get distinct roles and statuses present in the users table (excluding superadmin)
        $roles = User::where('role', '!=', 'superadmin')->distinct()->pluck('role');
        $statuses = User::where('role', '!=', 'superadmin')->distinct()->pluck('registration_status');


        return view('admin.users.index', [
            'users' => $users,
            'roles' => $roles,
            'statuses' => $statuses,
            'filters' => $request->all(), // Pass current filters to the view
        ]);
    }

    /**
     * Approve a user's registration.
     */
    public function approveUser(User $user) // Route model binding
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Unauthorized action.');
        }

        if ($user->registration_status !== 'pending') {
            return redirect()->route('admin.users.index')->with('error', 'User is not pending approval or has already been processed.');
        }

        $user->registration_status = 'approved';
        // Optionally, verify email if this step implies it for pending users
        // if (!$user->hasVerifiedEmail()) {
        //     $user->email_verified_at = now();
        // }
        $user->save();

        // Create a database notification for the approved student
        DbNotification::create([
            'user_id' => $user->id,
            'type' => 'RegistrationApprovedNotification', // Custom type string
            'data' => ['message' => 'Congratulations! Your registration has been approved. You can now log in and access all student features.']
        ]);

        // TODO: Dispatch Laravel Email Notification if needed (e.g., using App\Notifications\RegistrationApprovedNotification)
        // if (class_exists(\App\Notifications\RegistrationApprovedNotification::class)) {
        //     \Illuminate\Support\Facades\Notification::send($user, new \App\Notifications\RegistrationApprovedNotification());
        // }

        return redirect()->route('admin.users.index')->with('success', 'User registration approved successfully for ' . $user->email);
    }

    /**
     * Show the form for editing the registration window settings.
     */
    public function editRegistrationWindow()
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Unauthorized action.');
        }

        $window = RegistrationWindow::firstOrCreate([], [
            'start_time' => now()->addDay()->startOfHour(),
            'end_time' => now()->addDays(2)->startOfHour(),
            'max_registrations' => 0,
            'is_active' => false,
            'password' => null,
            'current_registrations' => 0,
            'first_use_time' => null,
        ]);
        return view('admin.registration_window.edit', compact('window'));
    }

    /**
     * Update the registration window settings from the web form.
     */
    public function updateRegistrationWindow(Request $request)
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Unauthorized action.');
        }

        $window = RegistrationWindow::firstOrCreate([], [ /* defaults if creating */ ]);

        // Custom rule to ensure password is confirmed if provided, but not required if blank
        $passwordRules = ['nullable', 'string', 'min:6', 'confirmed'];
        if (!$request->filled('password') && !$request->filled('password_confirmation')) {
            // If both are empty, password is not being changed, so validation passes for these fields.
            $passwordRules = ['nullable', 'string', 'min:6']; // Remove 'confirmed' if not changing
        }


        $validatedData = $request->validate([
            'start_time' => 'required|date_format:Y-m-d\TH:i', // For datetime-local input
            'end_time' => 'required|date_format:Y-m-d\TH:i|after:start_time',
            'max_registrations' => 'required|integer|min:0',
            'password' => $passwordRules,
            'is_active' => 'sometimes|boolean', // 'sometimes' because checkbox might not be sent if false
        ]);

        $updateData = [
            'start_time' => $validatedData['start_time'],
            'end_time' => $validatedData['end_time'],
            'max_registrations' => $validatedData['max_registrations'],
            'is_active' => $request->has('is_active'), // Convert checkbox value to boolean
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($validatedData['password']);
            // When password changes, reset first_use_time and current_registrations
            $updateData['first_use_time'] = null;
            $updateData['current_registrations'] = 0;
        }
        // No "else if exists and is null" for web form, as empty password field means "no change"

        if ($updateData['is_active']) {
            RegistrationWindow::where('id', '!=', $window->id)->update(['is_active' => false]);
        }

        // Consider if other fields changing should reset current_registrations/first_use_time
        // For example, if start_time is pushed to the future for an ongoing window.
        // For now, only password change triggers this reset.

        $window->update($updateData);

        return redirect()->route('admin.regwindow.edit')->with('success', 'Registration window updated successfully.');
    }

    /**
     * Display the admin dashboard with various statistics.
     */
    public function showDashboard()
    {
        if (Auth::user()->role !== 'superadmin') {
            abort(403, 'Unauthorized action.');
        }

        $stats = [
            'total_users' => User::where('role', '!=', 'superadmin')->count(),
            'total_jobs' => Job::count(),
            'pending_registrations' => User::where('registration_status', 'pending')->where('role', '!=', 'superadmin')->count(),
            'students_count' => User::whereIn('role', ['candidate', 'student'])->count(),
            'employers_count' => User::whereIn('role', ['employer', 'admin'])->where('role', '!=', 'superadmin')->count(),
            'students_with_cv_count' => User::whereIn('role', ['candidate', 'student'])->whereNotNull('cv')->count(),

            'jobs_by_location_all' => Job::select('location', DB::raw('count(*) as total'))
                                     ->whereNotNull('location')->where('location', '!=', '')
                                     ->groupBy('location')->orderBy('total', 'desc')->get(),

            'jobs_by_area_all' => Job::join('areas_of_interest', 'jobs_employment.area_of_interest_id', '=', 'areas_of_interest.id')
                                 ->select('areas_of_interest.name as area_name', DB::raw('count(jobs_employment.id) as total'))
                                 ->groupBy('areas_of_interest.name')->orderBy('total', 'desc')->get(),

            'jobs_by_month' => Job::select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as total'))
                                  ->groupBy('month')->orderBy('month', 'asc')->get(),

            'jobs_by_contract_type_all' => Job::select('contract_type', DB::raw('count(*) as total'))
                                         ->whereNotNull('contract_type')->where('contract_type', '!=', '')
                                         ->groupBy('contract_type')->orderBy('total', 'desc')->get(),

            'total_applications' => Application::count(),
        ];

        // Prepare top 5 for quick display, and all for charts if needed differently
        $stats['jobs_by_location_top5'] = $stats['jobs_by_location_all']->take(5);
        $stats['jobs_by_area_top5'] = $stats['jobs_by_area_all']->take(5);
        $stats['jobs_by_contract_type_top5'] = $stats['jobs_by_contract_type_all']->take(5);

        return view('admin.dashboard', compact('stats'));
    }
}
