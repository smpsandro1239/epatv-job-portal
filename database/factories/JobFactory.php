<?php

namespace Database\Factories;

use App\Models\AreaOfInterest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
  public function definition(): array
  {
    return [
      'company_id' => User::factory()->create(['role' => 'employer'])->id, // Assuming employers post jobs
      'title' => $this->faker->jobTitle,
      'category_id' => AreaOfInterest::inRandomOrder()->first()->id ?? AreaOfInterest::factory()->create()->id,
      'description' => $this->faker->paragraph,
      'location' => $this->faker->city,
      'salary' => $this->faker->randomFloat(2, 30000, 100000),
      'contract_type' => $this->faker->randomElement(['full-time', 'part-time']),
      'expiration_date' => now()->addDays(30),
      'area_of_interest_id' => AreaOfInterest::inRandomOrder()->first()->id ?? AreaOfInterest::factory()->create()->id,
      'posted_by' => User::inRandomOrder()->first()->id ?? User::factory()->create()->id,
      'status' => 'open',
    ];
  }
}
