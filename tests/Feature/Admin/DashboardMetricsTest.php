<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Job;
use App\Models\Application;
use App\Models\AreaOfInterest; // JobFactory might need this
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardMetricsTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        // Create and authenticate an admin user
        $this->adminUser = User::factory()->create(['role' => 'superadmin']);
        $this->actingAs($this->adminUser);
    }

    private function getDashboardStats(): array
    {
        $response = $this->get(route('admin.dashboard')); // Assuming route name is 'admin.dashboard'
        $response->assertOk();
        $response->assertViewIs('admin.dashboard');
        $response->assertViewHas('stats');
        return $response->viewData('stats');
    }

    public function test_active_job_seekers_count_is_correct()
    {
        User::factory()->count(3)->create(['role' => 'student']);
        User::factory()->count(2)->create(['role' => 'employer']);
        User::factory()->create(['role' => 'admin']); // Non-student

        $stats = $this->getDashboardStats();
        $this->assertEquals(3, $stats['active_job_seekers_count']);
    }

    public function test_active_job_seekers_count_is_zero()
    {
        User::factory()->count(2)->create(['role' => 'employer']);
        $stats = $this->getDashboardStats();
        $this->assertEquals(0, $stats['active_job_seekers_count']);
    }

    public function test_active_employers_count_is_correct()
    {
        User::factory()->count(4)->create(['role' => 'employer']);
        User::factory()->count(2)->create(['role' => 'student']);
        User::factory()->create(['role' => 'admin']); // Non-employer, not superadmin

        $stats = $this->getDashboardStats();
        $this->assertEquals(4, $stats['active_employers_count']);
    }

    public function test_active_employers_count_is_zero()
    {
        User::factory()->count(2)->create(['role' => 'student']);
        $stats = $this->getDashboardStats();
        $this->assertEquals(0, $stats['active_employers_count']);
    }

    public function test_student_profiles_completed_count_is_correct()
    {
        User::factory()->count(3)->create(['role' => 'student', 'cv' => 'path/to/cv.pdf']);
        User::factory()->count(1)->create(['role' => 'student', 'cv' => 'another/path.docx']);
        User::factory()->count(2)->create(['role' => 'student', 'cv' => null]);
        User::factory()->count(1)->create(['role' => 'student', 'cv' => '']); // Empty string
        User::factory()->create(['role' => 'employer', 'cv' => 'path/to/cv.pdf']); // Non-student with CV

        $stats = $this->getDashboardStats();
        $this->assertEquals(4, $stats['student_profiles_completed_count']); // 3 + 1
    }

    public function test_student_profiles_completed_count_is_zero()
    {
        User::factory()->count(2)->create(['role' => 'student', 'cv' => null]);
        User::factory()->count(1)->create(['role' => 'student', 'cv' => '']);
        User::factory()->create(['role' => 'employer', 'cv' => 'path/to/cv.pdf']);

        $stats = $this->getDashboardStats();
        $this->assertEquals(0, $stats['student_profiles_completed_count']);
    }

    public function test_total_applications_count_is_correct()
    {
        // Need to ensure dependent records for ApplicationFactory are created if any
        // For example, if ApplicationFactory creates a job_id and user_id
        $student = User::factory()->create(['role' => 'student']);
        $employer = User::factory()->create(['role' => 'employer']);
        $job = Job::factory()->create(['company_id' => $employer->id, 'posted_by' => $employer->id]);

        Application::factory()->count(5)->create([
            'user_id' => $student->id,
            'job_id' => $job->id,
        ]);

        $stats = $this->getDashboardStats();
        $this->assertEquals(5, $stats['total_applications']);
    }

    public function test_total_applications_count_is_zero()
    {
        $stats = $this->getDashboardStats();
        $this->assertEquals(0, $stats['total_applications']);
    }

    public function test_total_jobs_count_is_correct()
    {
        Job::factory()->count(6)->create();
        $stats = $this->getDashboardStats();
        $this->assertEquals(6, $stats['total_jobs']);
    }

    public function test_total_jobs_count_is_zero()
    {
        $stats = $this->getDashboardStats();
        $this->assertEquals(0, $stats['total_jobs']);
    }
}
