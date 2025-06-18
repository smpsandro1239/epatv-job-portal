<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AreaOfInterest;
use App\Models\Application;
use App\Models\User;

class Job extends Model
{
    use HasFactory;

    protected $table = 'jobs_employment';
    protected $fillable = [
        'title',
        'description',
        'company_id',
        'category_id',
        'area_of_interest_id',
        'posted_by',
        'location',
        'contract_type',
        'salary',
        'expiration_date',
    ];

    protected $casts = [
        'expiration_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(User::class, 'company_id');
    }

    public function category()
    {
        return $this->belongsTo(AreaOfInterest::class, 'category_id');
    }

    public function areaOfInterest()
    {
        return $this->belongsTo(AreaOfInterest::class, 'area_of_interest_id');
    }

    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'job_id');
    }

    public function savedByUsers()
    {
        return $this->belongsToMany(User::class, 'saved_jobs', 'job_id', 'user_id')->withTimestamps();
    }
}
