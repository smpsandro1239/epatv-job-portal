<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RegistrationWindow;
use App\Models\Notification as DbNotification; // Use an alias
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse; // Changed from Response
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator; // Using Validator facade for conditional logic
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule; // For Rule::in

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view with a specific type (student/employer).
     */
    public function createWithType(Request $request): View
    {
        $type = $request->route('type'); // 'student' or 'company' from route defaults
        return view('auth.register', ['type' => $type]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse // Changed return type
    {
        $baseRules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', Rule::in(['student', 'employer'])],
        ];

        $conditionalRules = [];
        $currentRole = $request->input('role');

        if ($currentRole === 'student') {
            $conditionalRules = [
                'course_completion_year' => ['nullable', 'integer', 'min:1980', 'max:' . (date('Y') + 7)],
                'window_password' => ['nullable', 'string'], // Further validation if window requires it
                'phone' => ['nullable', 'string', 'max:20'], // Added phone for student
            ];
        } elseif ($currentRole === 'employer') {
            $conditionalRules = [
                'company_name' => ['required', 'string', 'max:255'],
                'company_city' => ['required', 'string', 'max:255'], // Changed to required
                'company_website' => ['nullable', 'url', 'max:255'],
                'company_description' => ['nullable', 'string'],
                'company_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
                'phone' => ['required', 'string', 'max:20'], // Added phone
            ];
        }

        $validator = Validator::make($request->all(), array_merge($baseRules, $conditionalRules));

        if ($validator->fails()) {
            $redirectUrl = route('register'); // Default fallback
            if ($request->input('role') === 'student') {
                $redirectUrl = route('register.student');
            } elseif ($request->input('role') === 'employer') {
                $redirectUrl = route('register.company');
            }
            return redirect($redirectUrl)
                        ->withErrors($validator)
                        ->withInput();
        }

        $validatedData = $validator->validated();
        $registrationStatus = 'approved'; // Default status

        // --- Registration Window Logic for Students ---
        if ($validatedData['role'] === 'student') {
            $window = RegistrationWindow::where('is_active', true)
                // ->where('start_time', '<=', now()) // These time checks are for *during* registration attempt
                // ->where('end_time', '>=', now())
                ->first();

            $isWithinTime = $window && now()->between($window->start_time, $window->end_time);
            $hasCapacity = $window && $window->current_registrations < $window->max_registrations;
            $passwordRequired = $window && !empty($window->password);
            $passwordMatches = false;
            $isWithinFirstUseLimit = true; // Assume true if no password or not used yet

            if ($passwordRequired) {
                $passwordMatches = $request->filled('window_password') && Hash::check($request->input('window_password'), $window->password);
                if ($window->first_use_time) {
                    $isWithinFirstUseLimit = now()->diffInHours($window->first_use_time) < $window->password_valid_duration_hours ?? 2; // Assuming 2hr default
                }
            }

            // Determine final status based on window conditions
            if (!$window || !$isWithinTime || !$hasCapacity) {
                $registrationStatus = 'pending';
            } elseif ($passwordRequired) {
                if ($passwordMatches && $isWithinFirstUseLimit && $hasCapacity) {
                    // Valid password use, allow 'approved'
                    if (!$window->first_use_time) {
                        $window->update(['first_use_time' => now()]);
                    }
                    $window->increment('current_registrations');
                    $registrationStatus = 'approved';
                } else {
                    // Password required but failed match, or exceeded time/capacity after first use
                    $registrationStatus = 'pending';
                }
            } else {
                // No password required, and within time/capacity
                 if ($window && $hasCapacity && $isWithinTime) { // Double check capacity and time for non-password window
                    $window->increment('current_registrations');
                    $registrationStatus = 'approved';
                } else {
                    $registrationStatus = 'pending';
                }
            }
        } else { // Employer
            $registrationStatus = 'approved'; // Employers are auto-approved for now
        }

        // Prepare user data
        $userData = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'],
            'registration_status' => $registrationStatus,
        ];

        if ($validatedData['role'] === 'student') {
            $userData['course_completion_year'] = $validatedData['course_completion_year'] ?? null;
            $userData['phone'] = $validatedData['phone'] ?? null; // Added phone for student
            // window_password is not stored on user model
        } elseif ($validatedData['role'] === 'employer') {
            $userData['company_name'] = $validatedData['company_name'];
            $userData['company_city'] = $validatedData['company_city']; // No longer nullable in validation for employer
            $userData['company_website'] = $validatedData['company_website'] ?? null;
            $userData['company_description'] = $validatedData['company_description'] ?? null;
            $userData['phone'] = $validatedData['phone']; // Added phone

            if ($request->hasFile('company_logo')) {
                $path = $request->file('company_logo')->store('public/company_logos');
                $userData['company_logo'] = $path;
            }
        }

        $user = User::create($userData);

        // --- Create Notifications if Pending (for students) ---
        if ($user->role === 'student' && $user->registration_status === 'pending') {
            DbNotification::create([
                'user_id' => $user->id,
                'type' => 'PendingRegistrationNotification',
                'data' => ['message' => 'Your registration is pending approval by an administrator.']
            ]);

            $superAdmins = User::where('role', 'superadmin')->get();
            foreach ($superAdmins as $superAdmin) {
                DbNotification::create([
                    'user_id' => $superAdmin->id,
                    'type' => 'StudentPendingApprovalNotification',
                    'data' => [
                        'message' => "New student registration for {$user->name} ({$user->email}) requires approval.",
                        'student_id' => $user->id,
                        'student_name' => $user->name,
                        'student_email' => $user->email,
                    ]
                ]);
            }
        }

        event(new Registered($user));
        Auth::login($user);

        // Redirect based on role or status
        if ($user->role === 'student' && $user->registration_status === 'pending') {
            return redirect('/')->with('status', 'Registration received! Your application is pending approval.');
            // Or a specific pending page: redirect()->route('registration.pending');
        }

        // Determine redirect path based on role
        $redirectPath = match($user->role) {
            'student' => route('student.profile.show'), // Or student dashboard
            'employer' => route('employer.profile.show'), // Or employer dashboard
            default => '/', // Fallback
        };
        // The API controller returns response()->noContent() from Breeze API,
        // but for web, we redirect.
        return redirect($redirectPath)->with('success', 'Registration successful!');
    }
}
