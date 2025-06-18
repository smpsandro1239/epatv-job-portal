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

    // Ensure AreaOfInterest exists, or create one.
    $areaOfInterest = AreaOfInterest::inRandomOrder()->first();
    if (!$areaOfInterest) {
        $areaOfInterest = AreaOfInterest::factory()->create();
    }
    $areaOfInterestId = $areaOfInterest->id;

    // Ensure a category_id is always set. This assumes category_id refers to an AreaOfInterest.
    // If it's a different table, this logic would need adjustment.
    $categoryId = $areaOfInterestId; // Default to the same as area_of_interest_id for simplicity

    return [
      'company_id' => $companyId,
      'title' => $this->faker->jobTitle,
      'category_id' => $categoryId, // Ensure this is always set
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
