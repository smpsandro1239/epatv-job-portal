<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash; // Added this line

class RegistrationWindowFactory extends Factory
{
    public function definition(): array
    {
        return [
            'start_time' => now()->subDay(), // Changed from start_date
            'end_time' => now()->addDays(30),   // Changed from end_date
            'is_active' => true,
            'max_registrations' => 100,
            'password' => Hash::make('test_password'),
        ];
    }
}
