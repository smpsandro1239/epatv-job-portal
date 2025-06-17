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
        if (!isset($attributes['company_id'])) {
            $attributes['company_id'] = $this->createEmployer()->id;
        }
        if (!isset($attributes['posted_by'])) {
             $attributes['posted_by'] = $attributes['company_id'];
        }
        if (!isset($attributes['area_of_interest_id'])) {
            $attributes['area_of_interest_id'] = $this->createAreaOfInterest()->id;
        }
        return Job::factory()->create($attributes);
    }

    private function createApplication(User $student, Job $job, array $attributes = []): Application
    {
        return Application::factory()->create(array_merge([
            'user_id' => $student->id,
            'job_id' => $job->id,
        ], $attributes));
    }

    private function setupDashboardData()
    {
        // Users
        $this->createStudent(['registration_status' => 'pending', 'cv' => null]);
        $this->createStudent(['registration_status' => 'approved', 'cv' => 'path/to/cv1.pdf']);
        $this->createStudent(['registration_status' => 'approved', 'cv' => 'path/to/cv2.pdf']); // 2 students with CVs
        $this->createEmployer(['registration_status' => 'approved']);
        $this->createEmployer(['registration_status' => 'pending']); // 1 pending employer

        // Areas of Interest
        $areaTech = $this->createAreaOfInterest(['name' => 'Technology']);
        $areaMarketing = $this->createAreaOfInterest(['name' => 'Marketing']);

        // Jobs
        $this->createJob([
            'location' => 'New York',
            'area_of_interest_id' => $areaTech->id,
            'contract_type' => 'Full-Time',
            'created_at' => Carbon::now()->subMonths(1)
        ]);
        $this->createJob([
            'location' => 'New York',
            'area_of_interest_id' => $areaMarketing->id,
            'contract_type' => 'Part-Time',
            'created_at' => Carbon::now()
        ]);
        $this->createJob([
            'location' => 'London',
            'area_of_interest_id' => $areaTech->id,
            'contract_type' => 'Full-Time',
            'created_at' => Carbon::now()->subMonths(2)
        ]);

        // Applications
        $job1 = Job::first();
        $student1 = User::where('role', 'student')->first();
        if ($job1 && $student1) {
            $this->createApplication($student1, $job1);
            $this->createApplication($this->createStudent(['email' => 'another@student.com']), $job1); // 2 apps for job1
        }
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
            ->assertJsonCount(3, 'jobs_by_month') // Example check based on setup
            ->assertJsonPath('total_users', 5) // 3 students + 2 employers
            ->assertJsonPath('pending_registrations', 2) // 1 student + 1 employer
            ->assertJsonPath('students_count', 3)
            ->assertJsonPath('employers_count', 2)
            ->assertJsonPath('students_with_cv_count', 2)
            ->assertJsonPath('total_jobs', 3)
            ->assertJsonPath('total_applications', 2);

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
                 ->assertSeeTextInOrder([ // Check stat cards are rendering some values
                     'Total Users', '5',
                     'Total Jobs', '3',
                     'Pending Registrations', '2',
                     'Total Applications', '2',
                 ]);

        $stats = $response->viewData('stats');
        $this->assertEquals(5, $stats['total_users']);
        $this->assertEquals(3, $stats['students_count']);
        $this->assertEquals(2, $stats['employers_count']);
        $this->assertEquals(2, $stats['students_with_cv_count']);
        $this->assertCount(2, $stats['jobs_by_location_all']); // NY, London
        $this->assertCount(2, $stats['jobs_by_area_all']);   // Tech, Marketing
        $this->assertCount(2, $stats['jobs_by_contract_type_all']); // Full-Time, Part-Time
        $this->assertCount(3, $stats['jobs_by_month']);
    }

    public function test_dashboard_web_authorization()
    {
        $this->get('/admin/dashboard')->assertRedirect('/login'); // Unauthenticated

        $student = $this->createStudent();
        $this->actingAs($student)->get('/admin/dashboard')->assertStatus(403); // Wrong role
    }
}
