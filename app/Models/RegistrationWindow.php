<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationWindow extends Model
{
    protected $fillable = [
        'is_active',
        'start_time',
        'end_time',
        'max_registrations',
        'password',
        'first_use_time',
        'current_registrations',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'first_use_time' => 'datetime',
    ];
}
