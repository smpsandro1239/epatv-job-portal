<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\StudentController; // Added import
use App\Http\Controllers\JobController; // Added import

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
});
