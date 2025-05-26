<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
