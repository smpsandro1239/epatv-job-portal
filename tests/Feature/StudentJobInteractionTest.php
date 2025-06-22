<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Job;
use App\Models\AreaOfInterest;
use Tymon\JWTAuth\Facades\JWTAuth;

class StudentJobInteractionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private function createStudent(array $attributes = []): User
    {
        return User::factory()->create(array_merge(['role' => 'student'], $attributes));
    }

    private function createEmployer(array $attributes = []): User
    {
        return User::factory()->create(array_merge(['role' => 'employer'], $attributes));
    }

    private function createAreaOfInterest(array $attributes = []): AreaOfInterest
    {
        return AreaOfInterest::factory()->create($attributes);
    }

    private function createJob(array $attributes = []): Job
    {
        // Ensure default related models are created if not provided
        if (!isset($attributes['company_id'])) {
            $attributes['company_id'] = $this->createEmployer()->id;
        }
        if (!isset($attributes['posted_by'])) { // Assuming posted_by is also the employer for now
            $attributes['posted_by'] = $attributes['company_id'];
        }
        if (!isset($attributes['area_of_interest_id'])) {
            $attributes['area_of_interest_id'] = $this->createAreaOfInterest()->id;
        }
        return Job::factory()->create($attributes);
    }

    // ====== Web Tests for Job Listing and Filtering ======

    public function test_can_view_job_listing_page()
    {
        $this->createJob(['title' => 'Software Engineer']);
        $this->createJob(['title' => 'Product Manager']);

        $response = $this->get('/jobs');

        $response->assertStatus(200)
                 ->assertViewIs('jobs.index')
                 ->assertSee('Software Engineer')
                 ->assertSee('Product Manager');
    }

    public function test_job_listing_is_paginated()
    {
        Job::factory()->count(15)->create([ // Create more jobs than default pagination (10)
            'company_id' => $this->createEmployer()->id,
            'posted_by' => User::first()->id, // or specific ID
            'area_of_interest_id' => $this->createAreaOfInterest()->id,
        ]);

        $response = $this->get('/jobs');
        $response->assertStatus(200);
        // Check for pagination elements, specific class depends on pagination view
        $response->assertSeeText('Next');
    }

    public function test_filter_jobs_by_area_of_interest()
    {
        $area1 = $this->createAreaOfInterest(['name' => 'Tech']);
        $area2 = $this->createAreaOfInterest(['name' => 'Marketing']);

        $job1 = $this->createJob(['title' => 'Dev Job', 'area_of_interest_id' => $area1->id]);
        $job2 = $this->createJob(['title' => 'SEO Job', 'area_of_interest_id' => $area2->id]);

        $response = $this->get('/jobs?area_of_interest_id=' . $area1->id);

        $response->assertStatus(200)
                 ->assertSee($job1->title)
                 ->assertDontSee($job2->title);
    }

    public function test_filter_jobs_by_location()
    {
        $job1 = $this->createJob(['title' => 'Remote Job', 'location' => 'Remote']);
        $job2 = $this->createJob(['title' => 'Office Job', 'location' => 'New York']);

        $response = $this->get('/jobs?location=Remote');

        $response->assertStatus(200)
                 ->assertSee($job1->title)
                 ->assertDontSee($job2->title);
    }

    public function test_filter_jobs_by_contract_type()
    {
        $job1 = $this->createJob(['title' => 'Full-Time Gig', 'contract_type' => 'Full-Time']);
        $job2 = $this->createJob(['title' => 'Part-Time Role', 'contract_type' => 'Part-Time']);

        $response = $this->get('/jobs?contract_type=Full-Time');

        $response->assertStatus(200)
                 ->assertSee($job1->title)
                 ->assertDontSee($job2->title);
    }

    public function test_filter_jobs_with_multiple_filters()
    {
        $area = $this->createAreaOfInterest(['name' => 'Engineering']);
        $job1 = $this->createJob([
            'title' => 'Backend Dev NY Full-Time',
            'area_of_interest_id' => $area->id,
            'location' => 'New York',
            'contract_type' => 'Full-Time'
        ]);
        $job2 = $this->createJob([ // Different Area
            'title' => 'Frontend Dev NY Full-Time',
            'area_of_interest_id' => $this->createAreaOfInterest(['name' => 'Design'])->id,
            'location' => 'New York',
            'contract_type' => 'Full-Time'
        ]);
         $job3 = $this->createJob([ // Different Location
            'title' => 'Backend Dev Remote Full-Time',
            'area_of_interest_id' => $area->id,
            'location' => 'Remote',
            'contract_type' => 'Full-Time'
        ]);

        $response = $this->get('/jobs?area_of_interest_id=' . $area->id . '&location=New York&contract_type=Full-Time');

        $response->assertStatus(200)
                 ->assertSee($job1->title)
                 ->assertDontSee($job2->title)
                 ->assertDontSee($job3->title);
    }

    public function test_filter_values_are_retained_in_view()
    {
        $area = $this->createAreaOfInterest();
        // Create a job with specific values so they appear in the dropdowns
        $this->createJob([
            'area_of_interest_id' => $area->id,
            'location' => 'TestLocation',
            'contract_type' => 'TestType'
        ]);

        $response = $this->get('/jobs?area_of_interest_id=' . $area->id . '&location=TestLocation&contract_type=TestType');

        $response->assertStatus(200)
                 ->assertSee('value="' . $area->id . '" selected', false) // Check for selected option
                 ->assertSee('value="TestLocation" selected', false)     // For select dropdowns
                 // If location is text input: ->assertSee('value="TestLocation"', false)
                 ->assertSee('value="TestType" selected', false);
    }


    // ====== API Tests for Saving/Unsaving Jobs ======

    public function test_student_can_save_a_job_api()
    {
        $student = $this->createStudent();
        $job = $this->createJob();
        $token = JWTAuth::fromUser($student);

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->postJson("/api/student/jobs/{$job->id}/save");

        $response->assertStatus(200)
                 ->assertJson(['is_saved' => true]);
        $this->assertDatabaseHas('saved_jobs', [
            'user_id' => $student->id,
            'job_id' => $job->id,
        ]);
    }

    public function test_student_can_unsave_a_job_api()
    {
        $student = $this->createStudent();
        $job = $this->createJob();
        $student->savedJobs()->attach($job->id); // Pre-save the job
        $token = JWTAuth::fromUser($student);

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->postJson("/api/student/jobs/{$job->id}/save");

        $response->assertStatus(200)
                 ->assertJson(['is_saved' => false]);
        $this->assertDatabaseMissing('saved_jobs', [
            'user_id' => $student->id,
            'job_id' => $job->id,
        ]);
    }

    public function test_save_job_requires_authentication_api()
    {
        $job = $this->createJob();
        $response = $this->postJson("/api/student/jobs/{$job->id}/save");
        $response->assertStatus(401);
    }

    public function test_non_student_cannot_save_job_api()
    {
        $employer = $this->createEmployer();
        $job = $this->createJob();
        $token = JWTAuth::fromUser($employer);

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->postJson("/api/student/jobs/{$job->id}/save");
        $response->assertStatus(403);
    }

    public function test_saving_non_existent_job_returns_404_api()
    {
        $student = $this->createStudent();
        $token = JWTAuth::fromUser($student);

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->postJson("/api/student/jobs/9999/save"); // Non-existent job ID
        $response->assertStatus(404);
    }
}
