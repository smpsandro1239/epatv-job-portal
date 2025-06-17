<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Job;
use App\Models\AreaOfInterest;
use App\Models\Application;
use Tymon\JWTAuth\Facades\JWTAuth;

class StudentApplicationTest extends TestCase
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

    private function createJob(array $attributes = []): Job
    {
        if (!isset($attributes['company_id'])) {
            $attributes['company_id'] = $this->createEmployer()->id;
        }
        if (!isset($attributes['posted_by'])) {
             $attributes['posted_by'] = $attributes['company_id'];
        }
        if (!isset($attributes['area_of_interest_id'])) {
            $attributes['area_of_interest_id'] = AreaOfInterest::factory()->create()->id;
        }
        return Job::factory()->create($attributes);
    }

    private function createApplication(array $attributes = []): Application
    {
        if (!isset($attributes['user_id'])) {
            $attributes['user_id'] = $this->createStudent()->id;
        }
        if (!isset($attributes['job_id'])) {
            $attributes['job_id'] = $this->createJob()->id;
        }
        return Application::factory()->create($attributes);
    }

    // ====== API Tests for Viewing Student Applications ======

    public function test_student_can_list_own_applications_api()
    {
        $student = $this->createStudent();
        $job1 = $this->createJob();
        $job2 = $this->createJob();
        $this->createApplication(['user_id' => $student->id, 'job_id' => $job1->id]);
        $this->createApplication(['user_id' => $student->id, 'job_id' => $job2->id]);

        // Create an application for another student to ensure it's not listed
        $otherStudent = $this->createStudent();
        $this->createApplication(['user_id' => $otherStudent->id, 'job_id' => $job1->id]);

        $token = JWTAuth::fromUser($student);
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->getJson('/api/student/applications');

        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data') // Check pagination data array
                 ->assertJsonPath('data.0.user_id', $student->id) // Assuming default user details are not in this response structure
                 ->assertJsonPath('data.0.job.id', $job1->id) // Check eager loaded job details
                 ->assertJsonPath('data.1.job.id', $job2->id);
    }

    public function test_listing_student_applications_requires_authentication_api()
    {
        $response = $this->getJson('/api/student/applications');
        $response->assertStatus(401);
    }

    public function test_non_student_cannot_list_student_applications_api()
    {
        $employer = $this->createEmployer();
        $token = JWTAuth::fromUser($employer);
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->getJson('/api/student/applications');
        $response->assertStatus(403);
    }

    // ====== Web Tests for Viewing Student Applications ======

    public function test_student_can_list_own_applications_web()
    {
        $student = $this->createStudent();
        $job1 = $this->createJob();
        $this->createApplication(['user_id' => $student->id, 'job_id' => $job1->id, 'status' => 'Applied']);

        $response = $this->actingAs($student)->get('/student/applications');

        $response->assertStatus(200)
                 ->assertViewIs('student.applications.index')
                 ->assertViewHas('applications')
                 ->assertSee($job1->title)
                 ->assertSee('Applied');
    }

    public function test_listing_student_applications_requires_authentication_web()
    {
        $response = $this->get('/student/applications');
        $response->assertRedirect('/login');
    }

    public function test_non_student_cannot_list_student_applications_web()
    {
        $employer = $this->createEmployer();
        $response = $this->actingAs($employer)->get('/student/applications');
        $response->assertStatus(403);
    }

    // ====== API Tests for Job Application Submission ======

    public function test_student_can_apply_for_a_job_api()
    {
        $student = $this->createStudent();
        $job = $this->createJob();
        $token = JWTAuth::fromUser($student);
        $coverLetter = $this->faker->paragraph;

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->postJson('/api/apply', [
                             'job_id' => $job->id,
                             'cover_letter' => $coverLetter,
                         ]);

        $response->assertStatus(201)
                 ->assertJsonPath('application.job_id', $job->id)
                 ->assertJsonPath('application.user_id', $student->id)
                 ->assertJsonPath('application.status', 'pending')
                 ->assertJsonPath('application.cover_letter', $coverLetter);

        $this->assertDatabaseHas('applications', [
            'user_id' => $student->id,
            'job_id' => $job->id,
            'cover_letter' => $coverLetter,
            'status' => 'pending',
        ]);
    }

    public function test_application_submission_requires_job_id_api()
    {
        $student = $this->createStudent();
        $token = JWTAuth::fromUser($student);
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->postJson('/api/apply', ['cover_letter' => 'Test']);

        $response->assertStatus(422)->assertJsonValidationErrors('job_id');
    }

    public function test_application_submission_fails_for_non_existent_job_id_api()
    {
        $student = $this->createStudent();
        $token = JWTAuth::fromUser($student);
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->postJson('/api/apply', ['job_id' => 9999]);

        $response->assertStatus(422)->assertJsonValidationErrors('job_id');
    }

    public function test_application_submission_requires_authentication_api()
    {
        $job = $this->createJob();
        $response = $this->postJson('/api/apply', ['job_id' => $job->id]);
        $response->assertStatus(401);
    }
}
