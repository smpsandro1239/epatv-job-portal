<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
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

    public function company()
    {
        return $this->belongsTo(User::class, 'company_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function areaOfInterest()
    {
        return $this->belongsTo(AreaOfInterest::class, 'area_of_interest_id');
    }

    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }
}
