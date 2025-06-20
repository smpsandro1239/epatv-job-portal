<?php

namespace Tests\Feature\Api;

use App\Models\Job;
use App\Models\User; // Required if JobFactory creates users or for other setup
use App\Models\AreaOfInterest; // Required if JobFactory creates areas of interest
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the active job count endpoint.
     *
     * @return void
     */
    public function test_active_job_count_endpoint_returns_correct_count(): void
    {
        // Create active jobs
        Job::factory()->count(3)->create(['expiration_date' => now()->addDays(5)]);

        // Create active jobs with null expiration date
        Job::factory()->count(2)->create(['expiration_date' => null]);

        // Create inactive jobs
        Job::factory()->count(4)->create(['expiration_date' => now()->subDays(5)]);

        // Make a GET request to the API endpoint
        $response = $this->getJson('/api/jobs/active-count');

        // Assert the response status is 200 (OK)
        $response->assertStatus(200);

        // Assert the JSON response contains the correct active job count
        $response->assertJson(['active_job_count' => 5]); // 3 (future) + 2 (null)
    }

    /**
     * Test the active job count endpoint when there are no active jobs.
     *
     * @return void
     */
    public function test_active_job_count_endpoint_returns_zero_when_no_active_jobs(): void
    {
        // Create only inactive jobs
        Job::factory()->count(4)->create(['expiration_date' => now()->subDays(5)]);

        // Make a GET request to the API endpoint
        $response = $this->getJson('/api/jobs/active-count');

        // Assert the response status is 200 (OK)
        $response->assertStatus(200);

        // Assert the JSON response contains 0 active jobs
        $response->assertJson(['active_job_count' => 0]);
    }

    /**
     * Test the active job count endpoint when there are no jobs at all.
     *
     * @return void
     */
    public function test_active_job_count_endpoint_returns_zero_when_no_jobs_exist(): void
    {
        // No jobs created

        // Make a GET request to the API endpoint
        $response = $this->getJson('/api/jobs/active-count');

        // Assert the response status is 200 (OK)
        $response->assertStatus(200);

        // Assert the JSON response contains 0 active jobs
        $response->assertJson(['active_job_count' => 0]);
    }
}
