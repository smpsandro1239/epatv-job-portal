<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\StudentController; // Added import
use App\Http\Controllers\ApplicationController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware('signed')
    ->name('verification.verify');

Route::get('/jobs/active-count', [App\Http\Controllers\Api\JobController::class, 'activeCount'])->name('api.jobs.active_count');

// Route::middleware(['auth:api', 'role:admin,superadmin'])->group(function () {
    // Route::get('/admin/dashboard', [AdminController::class, 'dashboard']); // Moved to superadmin group
    // Note: Original task asked for superadmin only for user management.
    // Dashboard can remain admin,superadmin.
// });

// User Management and Dashboard by SuperAdmin
Route::middleware(['auth:api', 'role:superadmin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('api.admin.dashboard'); // Moved and renamed for clarity
    Route::get('/admin/users', [AdminController::class, 'index'])->name('api.admin.users.index');
    Route::post('/admin/users/{user}/approve', [AdminController::class, 'approveRegistration'])->name('api.admin.users.approve');

    // Registration Window Management by SuperAdmin
    Route::get('/admin/registration-window', [AdminController::class, 'getRegistrationWindow'])->name('api.admin.regwindow.show');
    Route::put('/admin/registration-window', [AdminController::class, 'updateRegistrationWindow'])->name('api.admin.regwindow.update');
});

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/user', function () {
        return response()->json(auth('api')->user());
    })->name('user');
    Route::post('/apply', [ApplicationController::class, 'apply'])->name('apply');

    // Student profile route
    Route::get('/student/profile', [StudentController::class, 'show'])
        ->middleware(['auth:api', 'role:student'])
        ->name('student.profile');
    Route::put('/student/profile', [StudentController::class, 'update'])
        ->middleware(['auth:api', 'role:student'])
        ->name('student.profile.update');

    // Route to toggle save job for student
    Route::post('/student/jobs/{job}/save', [StudentController::class, 'toggleSaveJob'])
        ->where('job', '[0-9]+') // Ensure job parameter is numeric
        ->middleware(['auth:api', 'role:student'])
        ->name('student.jobs.save');

    // Route to get student's job applications
    Route::get('/student/applications', [StudentController::class, 'getApplications'])
        ->middleware(['auth:api', 'role:student'])
        ->name('api.student.applications.index');
});

// Employer Profile API Routes
Route::middleware(['auth:api', 'role:employer'])->group(function () {
    Route::get('/employer/profile', [App\Http\Controllers\Api\EmployerController::class, 'show'])->name('api.employer.profile.show');
    Route::put('/employer/profile', [App\Http\Controllers\Api\EmployerController::class, 'update'])->name('api.employer.profile.update');
    // Using PUT for update. If form-data with PUT is an issue for some clients,
    // Route::post('/employer/profile', [App\Http\Controllers\Api\EmployerController::class, 'update'])->name('api.employer.profile.update.post');
    // could be an alternative, but PUT is semantically correct.
});

// Employer Job Management API Routes
Route::apiResource('/employer/jobs', App\Http\Controllers\Api\EmployerJobController::class)
    ->middleware(['auth:api', 'role:employer']);

// Employer Applications Listing API Route
Route::get('/employer/applications', [App\Http\Controllers\Api\EmployerController::class, 'listApplications'])
    ->middleware(['auth:api', 'role:employer'])
    ->name('api.employer.applications.index');

// General Authenticated User Notifications API Routes
Route::middleware('auth:api')->group(function () {
    Route::get('/notifications', [App\Http\Controllers\Api\NotificationController::class, 'index'])->name('api.notifications.index');
    Route::post('/notifications/{notification}/read', [App\Http\Controllers\Api\NotificationController::class, 'markAsRead'])->name('api.notifications.read');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead'])->name('api.notifications.markallasread');
});
