<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class AreaOfInterest extends Model
{
    use HasFactory;

    protected $table = 'areas_of_interest'; // Correct

    protected $fillable = [
        'name',
    ];

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_areas_of_interest', 'area_of_interest_id', 'user_id')->withTimestamps();
    }
}
