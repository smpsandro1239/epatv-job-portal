<?php

namespace Database\Factories;

use App\Models\AreaOfInterest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
  public function definition(): array
  {
    $companyId = User::factory()->create(['role' => 'employer'])->id;
    $areaOfInterestId = AreaOfInterest::inRandomOrder()->first()?->id ?? AreaOfInterest::factory()->create()->id;

    return [
      'company_id' => $companyId,
      'title' => $this->faker->jobTitle,
      // Both category_id and area_of_interest_id point to areas_of_interest table.
      // Setting them to potentially different areas or the same for simplicity.
      // If category_id has a distinct meaning (e.g. broader category), adjust as needed.
      'category_id' => AreaOfInterest::inRandomOrder()->first()?->id ?? AreaOfInterest::factory()->create()->id,
      'area_of_interest_id' => $areaOfInterestId,
      'description' => $this->faker->paragraph,
      'location' => $this->faker->city,
      'salary' => $this->faker->randomFloat(2, 30000, 100000) . ' USD', // Added currency for clarity if string
      'contract_type' => $this->faker->randomElement(['Full-Time', 'Part-Time', 'Contract', 'Internship']),
      'expiration_date' => $this->faker->dateTimeBetween('+1 week', '+3 months'),
      'posted_by' => $companyId, // Default posted_by to the company that owns the job
      // 'status' column does not exist on jobs_employment table, removing it.
    ];
  }
}
