<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationWindow extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_active',
        'start_time',
        'end_time',
        'max_registrations',
        'password',
        'password_valid_duration_hours', // Added
        'first_use_time',
        'current_registrations',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'first_use_time' => 'datetime',
        'max_registrations' => 'integer',
        'current_registrations' => 'integer',
        'password_valid_duration_hours' => 'integer',
    ];
}
