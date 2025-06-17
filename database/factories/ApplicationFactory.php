<?php

namespace Database\Factories;

use App\Models\Application;
use App\Models\User;
use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApplicationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Application::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Try to get existing users/jobs, or create new ones if none exist.
        $userId = User::where('role', 'student')->inRandomOrder()->first()?->id ?? User::factory()->create(['role' => 'student'])->id;
        $jobId = Job::inRandomOrder()->first()?->id ?? Job::factory()->create()->id;

        return [
            'user_id' => $userId,
            'job_id' => $jobId,
            'status' => $this->faker->randomElement(['pending', 'reviewed', 'shortlisted', 'rejected', 'hired']),
            'cover_letter' => $this->faker->optional()->paragraph, // Cover letter is nullable
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
