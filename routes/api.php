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
Route::middleware(['auth:api', 'role:admin,superadmin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
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
