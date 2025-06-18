<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\StudentController; // Added import
use App\Http\Controllers\JobController; // Added import
use App\Http\Controllers\EmployerController; // Added import for Employer Web Controller
use App\Http\Controllers\ApplicationController; // Added import

Route::view('/', 'welcome')->name('home');
Route::view('/login', 'auth.login')->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
Route::view('/register', 'auth.register')->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

// Updated Jobs Index Route
Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');

// Student Profile Web Routes
Route::middleware(['auth', 'role:student'])->group(function () {
    Route::get('/student/profile', [StudentController::class, 'show'])->name('student.profile.show');
    Route::get('/student/profile/edit', [StudentController::class, 'edit'])->name('student.profile.edit');
    Route::put('/student/profile', [StudentController::class, 'update'])->name('student.profile.update');
    Route::get('/student/applications', [StudentController::class, 'listApplications'])->name('student.applications.index');

    // Job Application Routes for Students
    Route::get('/jobs/{job}/apply', [ApplicationController::class, 'create'])->name('jobs.application.create');
    Route::post('/jobs/{job}/apply', [ApplicationController::class, 'store'])->name('jobs.application.store');
});

// Employer Profile Web Routes
Route::middleware(['auth', 'role:employer'])->group(function () {
    Route::get('/employer/profile', [EmployerController::class, 'show'])->name('employer.profile.show');
    Route::get('/employer/profile/edit', [EmployerController::class, 'edit'])->name('employer.profile.edit');
    Route::put('/employer/profile', [EmployerController::class, 'update'])->name('employer.profile.update');
});

// Employer Job Management Web Routes
Route::middleware(['auth', 'role:employer'])->group(function () {
    Route::resource('/employer/jobs', \App\Http\Controllers\EmployerJobController::class);
    // Note: If EmployerJobController was not in App\Http\Controllers, the FQCN would be needed.
    // For this project, it's assumed to be in the default Controllers namespace.

    // Employer Applications Listing Web Route
    Route::get('/employer/applications', [\App\Http\Controllers\EmployerController::class, 'listApplications'])
         ->name('employer.applications.index');
});

// Admin (SuperAdmin) User Management Web Routes
Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::get('/admin/users', [\App\Http\Controllers\AdminController::class, 'listUsers'])->name('admin.users.index');
    Route::post('/admin/users/{user}/approve', [\App\Http\Controllers\AdminController::class, 'approveUser'])->name('admin.users.approve');

    // Admin (SuperAdmin) Registration Window Management Web Routes
    Route::get('/admin/registration-window', [\App\Http\Controllers\AdminController::class, 'editRegistrationWindow'])->name('admin.regwindow.edit');
    Route::put('/admin/registration-window', [\App\Http\Controllers\AdminController::class, 'updateRegistrationWindow'])->name('admin.regwindow.update');

    // Admin (SuperAdmin) Dashboard Web Route
    Route::get('/admin/dashboard', [\App\Http\Controllers\AdminController::class, 'showDashboard'])->name('admin.dashboard');
});

// General Authenticated User Notifications Web Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markallasread');
});
