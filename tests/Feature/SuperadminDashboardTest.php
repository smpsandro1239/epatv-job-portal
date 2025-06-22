<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Job;
use App\Models\Application;
use App\Models\AreaOfInterest;
use Illuminate\Support\Facades\DB; // For direct DB checks if needed, though model counts are better
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;

class SuperadminDashboardTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private function createSuperadmin(array $attributes = []): User
    {
        return User::factory()->create(array_merge(['role' => 'superadmin'], $attributes));
    }

    private function createStudent(array $attributes = []): User
    {
        return User::factory()->create(array_merge(['role' => 'student'], $attributes));
    }

    private function createEmployer(array $attributes = []): User
    {
        return User::factory()->create(array_merge(['role' => 'employer', 'company_name' => $this->faker->company], $attributes));
    }

    private function createAreaOfInterest(array $attributes = []): AreaOfInterest
    {
        return AreaOfInterest::factory()->create($attributes);
    }

    private function createJob(array $attributes = []): Job
    {
        if (!isset($attributes['company_id']) || !isset($attributes['posted_by'])) {
            // Or throw an exception if these are always required for this helper's usage in tests
            $employer = $this->createEmployer(); // This line might still create an extra employer if not careful in setupDashboardData
            $attributes['company_id'] = $attributes['company_id'] ?? $employer->id;
            $attributes['posted_by'] = $attributes['posted_by'] ?? $employer->id;
        }
        if (!isset($attributes['area_of_interest_id'])) {
            $attributes['area_of_interest_id'] = $this->createAreaOfInterest()->id;
        }
        return Job::factory()->create($attributes);
    }

    public function createTestApplication(User $student, Job $job, array $attributes = []): Application
    {
        return Application::factory()->create(array_merge([
            'user_id' => $student->id,
            'job_id' => $job->id,
        ], $attributes));
    }

    private function setupDashboardData()
    {
        // Users - Total 6 non-superadmin users
        $student1 = $this->createStudent(['registration_status' => 'pending', 'cv' => null, 'email' => 's1@example.com']);
        $student2 = $this->createStudent(['registration_status' => 'approved', 'cv' => 'path/to/cv1.pdf', 'email' => 's2@example.com']);
        $student3 = $this->createStudent(['registration_status' => 'approved', 'cv' => 'path/to/cv2.pdf', 'email' => 's3@example.com']);
        $employer1 = $this->createEmployer(['registration_status' => 'approved', 'email' => 'e1@example.com']);
        $employer2 = $this->createEmployer(['registration_status' => 'pending', 'email' => 'e2@example.com']);
        $studentForApp = $this->createStudent(['email' => 'another@student.com', 'cv' => 'path/to/cv_app.pdf']); // Student with CV for application stats

        // Areas of Interest
        $areaTech = $this->createAreaOfInterest(['name' => 'Technology']);
        $areaMarketing = $this->createAreaOfInterest(['name' => 'Marketing']);

        // Jobs
        $job1 = $this->createJob([
            'company_id' => $employer1->id, 'posted_by' => $employer1->id,
            'location' => 'New York', 'area_of_interest_id' => $areaTech->id,
            'contract_type' => 'Full-Time',
            'created_at' => Carbon::now()->subMonths(1)
        ]);
        $this->createJob([
            'company_id' => $employer1->id, 'posted_by' => $employer1->id,
            'location' => 'New York', 'area_of_interest_id' => $areaMarketing->id,
            'contract_type' => 'Part-Time',
            'created_at' => Carbon::now()
        ]);
        $this->createJob([
            'company_id' => $employer2->id, 'posted_by' => $employer2->id,
            'location' => 'London', 'area_of_interest_id' => $areaTech->id,
            'contract_type' => 'Full-Time',
            'created_at' => Carbon::now()->subMonths(2)
        ]);

        // Applications
        // $job1 is already defined from above if that's the one we want to use for applications.
        // Using $studentForApp for applications.
        if ($job1 && $studentForApp) { // $job1 was defined when creating jobs for $employer1
            $this->createTestApplication($studentForApp, $job1);
            // If we need a second application for the 'total_applications' count:
            // Let's use another existing student for the second application to keep user count controlled.
             if ($student2) { // $student2 was created earlier
                 $this->createTestApplication($student2, $job1);
             }
        }
        // With this setup:
        // Users: $student1, $student2, $student3, $employer1, $employer2, $studentForApp = 6 users
        // Pending: $student1, $employer2 = 2 pending
        // Students: $student1, $student2, $student3, $studentForApp = 4 students
        // Employers: $employer1, $employer2 = 2 employers
        // Students with CV: $student2, $student3, $studentForApp = 3 students with CV
        // Total Jobs: 3
        // Total Applications: 2 (if $student2 exists, otherwise 1)
    }


    // ====== API Tests ======

    public function test_superadmin_can_get_dashboard_data_api()
    {
        $superadmin = $this->createSuperadmin();
        $this->setupDashboardData();
        $token = JWTAuth::fromUser($superadmin);

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->getJson('/api/admin/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'total_users', 'total_jobs', 'pending_registrations',
                'students_count', 'employers_count', 'students_with_cv_count',
                'total_applications',
                'jobs_by_location', 'jobs_by_area', 'jobs_by_month', 'jobs_by_contract_type'
            ])
            ->assertJsonCount(3, 'jobs_by_month')
            ->assertJsonPath('total_users', 6) // Updated count
            ->assertJsonPath('pending_registrations', 2)
            ->assertJsonPath('students_count', 4) // Updated count
            ->assertJsonPath('employers_count', 2)
            ->assertJsonPath('students_with_cv_count', 3) // Updated count
            ->assertJsonPath('total_jobs', 3)
            ->assertJsonPath('total_applications', 2); // Updated count

        // Example check for grouped data structure
        $response->assertJsonPath('jobs_by_location.0.location', 'New York'); // Assuming NY has most jobs
        $response->assertJsonPath('jobs_by_location.0.total', 2);
    }

    public function test_dashboard_api_authorization()
    {
        $this->getJson('/api/admin/dashboard')->assertStatus(401); // Unauthenticated

        $student = $this->createStudent();
        $token = JWTAuth::fromUser($student);
        $this->withHeaders(['Authorization' => "Bearer {$token}"])
             ->getJson('/api/admin/dashboard')->assertStatus(403); // Wrong role
    }

    // ====== Web Tests ======

    public function test_superadmin_can_view_dashboard_web()
    {
        $superadmin = $this->createSuperadmin();
        $this->setupDashboardData();

        $response = $this->actingAs($superadmin)->get('/admin/dashboard');

        $response->assertStatus(200)
                 ->assertViewIs('admin.dashboard')
                 ->assertViewHas('stats')
                 ->assertSeeTextInOrder([
                     'Total Users', '6',
                     'Total Jobs', '3',
                     'Pending Registrations', '2',
                     'Total Applications', '2',
                 ]);

        $stats = $response->viewData('stats');
        $this->assertEquals(6, $stats['total_users']);
        $this->assertEquals(4, $stats['active_job_seekers_count']); // Key corrected
        $this->assertEquals(2, $stats['active_employers_count']); // Key corrected
        $this->assertEquals(3, $stats['student_profiles_completed_count']); // Key corrected
        $this->assertCount(2, $stats['jobs_by_location_all']);
        $this->assertCount(2, $stats['jobs_by_area_all']);
        $this->assertCount(2, $stats['jobs_by_contract_type_all']);
        $this->assertCount(3, $stats['jobs_by_month']);
    }

    public function test_dashboard_web_authorization()
    {
        $this->get('/admin/dashboard')->assertRedirect('/login'); // Unauthenticated

        $student = $this->createStudent();
        $this->actingAs($student)->get('/admin/dashboard')->assertStatus(403); // Wrong role
    }
}
