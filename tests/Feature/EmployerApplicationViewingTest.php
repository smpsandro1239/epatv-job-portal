<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Job;
use App\Models\AreaOfInterest;
use App\Models\Application;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class EmployerApplicationViewingTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public'); // For CVs if they are involved in what's displayed
    }

    private function createEmployer(array $attributes = []): User
    {
        return User::factory()->create(array_merge(['role' => 'employer', 'company_name' => $this->faker->company], $attributes));
    }

    private function createStudent(array $attributes = []): User
    {
        // Ensure CV path is plausible if we check for it
        $cvPath = 'cvs/' . $this->faker->uuid . '.pdf';
        // Storage::disk('public')->put($cvPath, 'fake_cv_content'); // Not strictly needed for test unless file existence check is done

        return User::factory()->create(array_merge([
            'role' => 'student',
            'cv' => $attributes['cv'] ?? $cvPath, // Allow overriding CV path
        ], $attributes));
    }

    private function createAreaOfInterest(array $attributes = []): AreaOfInterest
    {
        return AreaOfInterest::factory()->create($attributes);
    }

    private function createJobForEmployer(User $employer, array $attributes = []): Job
    {
        return Job::factory()->create(array_merge([
            'company_id' => $employer->id,
            'posted_by' => $employer->id,
            'area_of_interest_id' => $this->createAreaOfInterest()->id,
        ], $attributes));
    }

    private function createApplicationRecord(User $student, Job $job, array $attributes = []): Application
    {
        return Application::factory()->create(array_merge([
            'user_id' => $student->id,
            'job_id' => $job->id,
        ], $attributes));
    }

    // ====== API Tests ======

    public function test_employer_can_list_applications_for_their_jobs_api()
    {
        $employer1 = $this->createEmployer();
        $job1 = $this->createJobForEmployer($employer1);
        $job2 = $this->createJobForEmployer($employer1);

        $student1 = $this->createStudent();
        $student2 = $this->createStudent();

        $app1 = $this->createApplicationRecord($student1, $job1);
        $app2 = $this->createApplicationRecord($student2, $job2);
        // Application for another employer's job
        $employer2 = $this->createEmployer();
        $job_other_employer = $this->createJobForEmployer($employer2);
        $this->createApplicationRecord($student1, $job_other_employer);

        $token = JWTAuth::fromUser($employer1);
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->getJson('/api/employer/applications');

        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data') // Expecting 2 applications for employer1
                 ->assertJsonPath('data.0.id', $app2->id) // latest first
                 ->assertJsonPath('data.1.id', $app1->id)
                 ->assertJsonPath('data.0.user.name', $student2->name)
                 ->assertJsonPath('data.0.job.title', $job2->title);
    }

    public function test_employer_sees_paginated_applications_api()
    {
        $employer = $this->createEmployer();
        $job = $this->createJobForEmployer($employer);
        for ($i=0; $i < 20; $i++) {
            $this->createApplicationRecord($this->createStudent(['email' => "student{$i}@example.com"]), $job);
        }

        $token = JWTAuth::fromUser($employer);
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->getJson('/api/employer/applications');

        $response->assertStatus(200)
                 ->assertJsonCount(15, 'data') // Default pagination is 15 for API
                 ->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_list_employer_applications_requires_authentication_api()
    {
        $response = $this->getJson('/api/employer/applications');
        $response->assertStatus(401);
    }

    public function test_non_employer_cannot_list_employer_applications_api()
    {
        $student = $this->createStudent();
        $token = JWTAuth::fromUser($student);
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->getJson('/api/employer/applications');
        $response->assertStatus(403);
    }

    // ====== Web Tests ======

    public function test_employer_can_list_applications_for_their_jobs_web()
    {
        $employer1 = $this->createEmployer();
        $job1 = $this->createJobForEmployer($employer1);
        $student1 = $this->createStudent(['cv' => 'cvs/student1_cv.pdf']); // Ensure CV path
        Storage::disk('public')->put($student1->cv, 'fake cv'); // Create fake file

        $app1 = $this->createApplicationRecord($student1, $job1);

        // Application for another employer's job
        $this->createApplicationRecord($this->createStudent(), $this->createJobForEmployer($this->createEmployer()));

        $response = $this->actingAs($employer1)->get('/employer/applications');

        $response->assertStatus(200)
                 ->assertViewIs('employer.applications.index')
                 ->assertViewHas('applications', function($applications) use ($app1) {
                     return $applications->contains($app1);
                 })
                 ->assertSee($student1->name)
                 ->assertSee($job1->title)
                 ->assertSee(Storage::url($student1->cv)); // Check for CV link
    }

    public function test_employer_sees_paginated_applications_web()
    {
        $employer = $this->createEmployer();
        $job = $this->createJobForEmployer($employer);
        for ($i=0; $i < 12; $i++) { // Default web pagination is 10
            $this->createApplicationRecord($this->createStudent(['email' => "student{$i}web@example.com"]), $job);
        }

        $response = $this->actingAs($employer)->get('/employer/applications');

        $response->assertStatus(200)
                 ->assertViewHas('applications', function($applications) {
                     return $applications->count() == 10; // Web pagination is 10
                 });
        $response->assertSee('pagination');
    }

    public function test_list_employer_applications_requires_authentication_web()
    {
        $response = $this->get('/employer/applications');
        $response->assertRedirect('/login');
    }

    public function test_non_employer_cannot_list_employer_applications_web()
    {
        $student = $this->createStudent();
        $response = $this->actingAs($student)->get('/employer/applications');
        $response->assertStatus(403);
    }
}
