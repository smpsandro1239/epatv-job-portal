<?php

namespace Database\Factories;

use App\Models\AreaOfInterest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
  public function definition(): array
  {
    // Ensure AreaOfInterest exists, or create one.
    $areaOfInterest = AreaOfInterest::inRandomOrder()->first() ?? AreaOfInterest::factory()->create();
    $areaOfInterestId = $areaOfInterest->id;
    $categoryId = $areaOfInterestId; // Default to the same as area_of_interest_id for simplicity

    return [
      'company_id' => User::factory()->state(['role' => 'employer']), // Will be overridden if company_id is provided in create()
      'posted_by' => function (array $attributes) {
          // Default posted_by to the company_id if not provided,
          // assuming company_id is resolved (either from factory or create() override)
          return $attributes['company_id'];
      },
      'title' => $this->faker->jobTitle,
      'category_id' => $categoryId, // Ensure this is always set
      'area_of_interest_id' => $areaOfInterestId,
      'description' => $this->faker->paragraph,
      'location' => $this->faker->city,
      'salary' => $this->faker->randomFloat(2, 30000, 100000) . ' USD',
      'contract_type' => $this->faker->randomElement(['Full-Time', 'Part-Time', 'Contract', 'Internship']),
      'expiration_date' => $this->faker->dateTimeBetween('+1 week', '+3 months'),
      // 'status' column does not exist on jobs_employment table, removing it.
    ];
  }
}
