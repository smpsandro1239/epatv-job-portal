<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Models\Application;
use App\Models\Job;
use App\Models\AreaOfInterest;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'registration_status',
        'email_verified_at',
        'phone',
        'course_completion_year',
        'photo', // Added
        'cv',    // Added
        'company_name',
        'company_city',
        'company_website',
        'company_description',
        'company_logo', // Added for company logo
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'role' => 'string',
        'registration_status' => 'string',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return ['role' => $this->role];
    }

    // User as a candidate
    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function savedJobs()
    {
        return $this->belongsToMany(Job::class, 'saved_jobs', 'user_id', 'job_id');
    }

    public function areasOfInterest()
    {
        return $this->belongsToMany(AreaOfInterest::class, 'user_areas_of_interest', 'user_id', 'area_of_interest_id');
    }

    // User as an employer/company
    public function postedJobs()
    {
        return $this->hasMany(Job::class, 'company_id');
    }

    /**
     * Get the database notifications for the user.
     */
    // public function notifications()
    // {
    //     // Using App\Models\Notification directly, alias if needed at the top of the file
    //     return $this->hasMany(\App\Models\Notification::class)->orderBy('created_at', 'desc');
    // }
}
