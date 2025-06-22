<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Job;
use App\Models\AreaOfInterest;
use Tymon\JWTAuth\Facades\JWTAuth;

class EmployerJobManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private function createEmployer(array $attributes = []): User
    {
        return User::factory()->create(array_merge(['role' => 'employer', 'company_name' => $this->faker->company], $attributes));
    }

    private function createStudent(array $attributes = []): User
    {
        return User::factory()->create(array_merge(['role' => 'student'], $attributes));
    }

    private function createAreaOfInterest(array $attributes = []): AreaOfInterest
    {
        return AreaOfInterest::factory()->create($attributes);
    }

    private function createJobForEmployer(User $employer, array $attributes = []): Job
    {
        return Job::factory()->create(array_merge([
            'company_id' => $employer->id,
            'posted_by' => $employer->id, // Assuming employer posts their own jobs
            'area_of_interest_id' => $this->createAreaOfInterest()->id,
        ], $attributes));
    }

    private function getJobData(array $overrides = []): array
    {
        return array_merge([
            'title' => $this->faker->jobTitle,
            'area_of_interest_id' => $this->createAreaOfInterest()->id,
            'description' => $this->faker->paragraph,
            'location' => $this->faker->city,
            'contract_type' => 'Full-Time',
            'salary' => 'Negotiable',
            'expiration_date' => now()->addMonths(1)->format('Y-m-d H:i:s'),
        ], $overrides);
    }

    // ====== API Tests ======

    public function test_employer_can_list_own_jobs_api()
    {
        $employer1 = $this->createEmployer();

        // Ensure $job1 is older than $job2
        $job1 = $this->createJobForEmployer($employer1, [
            'title' => 'Job 1 by Employer 1',
            'created_at' => now()->subMinute() // Older
        ]);
        $job2 = $this->createJobForEmployer($employer1, [
            'title' => 'Job 2 by Employer 1',
            'created_at' => now() // Newer
        ]);

        $employer2 = $this->createEmployer();
        $this->createJobForEmployer($employer2, ['title' => 'Job by Employer 2']);

        $token = JWTAuth::fromUser($employer1);
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->getJson('/api/employer/jobs');

        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data')
                 ->assertJsonPath('data.0.title', $job2->title) // Expect $job2 (newer) first
                 ->assertJsonPath('data.1.title', $job1->title); // Expect $job1 (older) second
    }

    public function test_employer_can_create_job_api()
    {
        $employer = $this->createEmployer();
        $token = JWTAuth::fromUser($employer);
        $jobData = $this->getJobData();

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->postJson('/api/employer/jobs', $jobData);

        $response->assertStatus(201)
                 ->assertJsonPath('title', $jobData['title'])
                 ->assertJsonPath('company_id', $employer->id);
        $this->assertDatabaseHas('jobs_employment', ['title' => $jobData['title'], 'company_id' => $employer->id]);
    }

    public function test_create_job_validation_api()
    {
        $employer = $this->createEmployer();
        $token = JWTAuth::fromUser($employer);
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->postJson('/api/employer/jobs', ['title' => '']); // Missing other required fields
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['title', 'area_of_interest_id', 'description']);
    }

    public function test_employer_can_show_own_job_api()
    {
        $employer = $this->createEmployer();
        $job = $this->createJobForEmployer($employer);
        $token = JWTAuth::fromUser($employer);

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->getJson("/api/employer/jobs/{$job->id}");
        $response->assertStatus(200)->assertJsonPath('id', $job->id);
    }

    public function test_employer_cannot_show_others_job_api()
    {
        $employer1 = $this->createEmployer();
        $employer2 = $this->createEmployer();
        $jobOfEmployer2 = $this->createJobForEmployer($employer2);
        $token = JWTAuth::fromUser($employer1);

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->getJson("/api/employer/jobs/{$jobOfEmployer2->id}");
        $response->assertStatus(403);
    }

    public function test_employer_can_update_own_job_api()
    {
        $employer = $this->createEmployer();
        $job = $this->createJobForEmployer($employer);
        $token = JWTAuth::fromUser($employer);
        $updateData = ['title' => 'Updated Job Title API'];

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->putJson("/api/employer/jobs/{$job->id}", $updateData);
        $response->assertStatus(200)->assertJsonPath('title', 'Updated Job Title API');
        $this->assertDatabaseHas('jobs_employment', ['id' => $job->id, 'title' => 'Updated Job Title API']);
    }

    public function test_employer_can_delete_own_job_api()
    {
        $employer = $this->createEmployer();
        $job = $this->createJobForEmployer($employer);
        $token = JWTAuth::fromUser($employer);

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->deleteJson("/api/employer/jobs/{$job->id}");
        $response->assertStatus(200); // Or 204 if no content returned
        $this->assertDatabaseMissing('jobs_employment', ['id' => $job->id]);
    }

    public function test_employer_job_management_api_authorization()
    {
        $this->getJson('/api/employer/jobs')->assertStatus(401); // Unauthenticated

        $student = $this->createStudent();
        $token = JWTAuth::fromUser($student);
        $this->withHeaders(['Authorization' => "Bearer {$token}"])
             ->getJson('/api/employer/jobs')->assertStatus(403); // Wrong role
    }


    // ====== Web Tests ======

    public function test_employer_can_list_own_jobs_web()
    {
        $employer = $this->createEmployer();
        $job1 = $this->createJobForEmployer($employer, ['title' => 'Web Job 1']);
        $this->createJobForEmployer($this->createEmployer(), ['title' => 'Other Employer Job']); // Another employer's job

        $response = $this->actingAs($employer)->get('/employer/jobs');
        $response->assertStatus(200)
                 ->assertViewIs('employer.jobs.index')
                 ->assertSee($job1->title)
                 ->assertDontSee('Other Employer Job');
    }

    public function test_employer_can_view_create_job_form_web()
    {
        $employer = $this->createEmployer();
        $response = $this->actingAs($employer)->get('/employer/jobs/create');
        $response->assertStatus(200)->assertViewIs('employer.jobs.create');
    }

    public function test_employer_can_create_job_web()
    {
        $employer = $this->createEmployer();
        $jobData = $this->getJobData(['title' => 'New Web Job']);

        $response = $this->actingAs($employer)->post('/employer/jobs', $jobData);
        $response->assertRedirect('/employer/jobs')->assertSessionHas('success');
        $this->assertDatabaseHas('jobs_employment', ['title' => 'New Web Job', 'company_id' => $employer->id]);
    }

    public function test_employer_can_view_edit_own_job_form_web()
    {
        $employer = $this->createEmployer();
        $job = $this->createJobForEmployer($employer);
        $response = $this->actingAs($employer)->get("/employer/jobs/{$job->id}/edit");
        $response->assertStatus(200)->assertViewIs('employer.jobs.edit');
    }

    public function test_employer_cannot_view_edit_others_job_form_web()
    {
        $employer1 = $this->createEmployer();
        $jobOfOther = $this->createJobForEmployer($this->createEmployer());
        $response = $this->actingAs($employer1)->get("/employer/jobs/{$jobOfOther->id}/edit");
        $response->assertStatus(403);
    }

    public function test_employer_can_update_own_job_web()
    {
        $employer = $this->createEmployer();
        $job = $this->createJobForEmployer($employer);
        $updateData = $this->getJobData(['title' => 'Updated Web Job Title']);

        $response = $this->actingAs($employer)->put("/employer/jobs/{$job->id}", $updateData);
        $response->assertRedirect('/employer/jobs')->assertSessionHas('success');
        $this->assertDatabaseHas('jobs_employment', ['id' => $job->id, 'title' => 'Updated Web Job Title']);
    }

    public function test_employer_can_delete_own_job_web()
    {
        $employer = $this->createEmployer();
        $job = $this->createJobForEmployer($employer);

        $response = $this->actingAs($employer)->delete("/employer/jobs/{$job->id}");
        $response->assertRedirect('/employer/jobs')->assertSessionHas('success');
        $this->assertDatabaseMissing('jobs_employment', ['id' => $job->id]);
    }

    public function test_employer_job_management_web_authorization()
    {
        $this->get('/employer/jobs')->assertRedirect('/login'); // Unauthenticated

        $student = $this->createStudent();
        $this->actingAs($student)->get('/employer/jobs')->assertStatus(403); // Wrong role
    }
}
