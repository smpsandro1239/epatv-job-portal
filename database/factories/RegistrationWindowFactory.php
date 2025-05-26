<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RegistrationWindowFactory extends Factory
{
    public function definition(): array
    {
        return [
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(30),
        ];
    }
}
