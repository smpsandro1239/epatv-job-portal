<?php

namespace Tests\Feature\Student;

use App\Models\User;
use App\Models\Job;
use App\Models\Application;
use App\Models\AreaOfInterest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class DashboardMetricsTest extends TestCase
{
    use RefreshDatabase;

    private User $studentUser;

    protected function setUp(): void
    {
        parent::setUp();
        // Create and authenticate a student user
        $this->studentUser = User::factory()->create(['role' => 'student']);
        $this->actingAs($this->studentUser);
    }

    private function getStudentProfileShowViewData(): array
    {
        // The route 'student.profile.show' does not take parameters in web.php for the GET request
        // as it's based on Auth::user()
        $response = $this->get(route('student.profile.show'));
        $response->assertOk();
        $response->assertViewIs('student.profile.show');
        $response->assertViewHasAll(['user', 'myApplicationsCount', 'jobsInPreferredAreasCount']);

        // Extract all view data
        // $viewData = $response->viewData(null); // Old problematic line

        return [
            'user' => $response->viewData('user'),
            'myApplicationsCount' => $response->viewData('myApplicationsCount'),
            'jobsInPreferredAreasCount' => $response->viewData('jobsInPreferredAreasCount'),
        ];
    }

    // Tests for "My Submitted Applications"
    public function test_my_applications_count_is_correct()
    {
        // Applications for the authenticated student
        Application::factory()->count(3)->create(['user_id' => $this->studentUser->id]);

        // Applications for another student (control)
        $otherStudent = User::factory()->create(['role' => 'student']);
        Application::factory()->count(2)->create(['user_id' => $otherStudent->id]);

        $viewData = $this->getStudentProfileShowViewData();
        $this->assertEquals(3, $viewData['myApplicationsCount']);
        $this->assertEquals($this->studentUser->id, $viewData['user']->id);
    }

    public function test_my_applications_count_is_zero()
    {
        // Applications for another student (control)
        $otherStudent = User::factory()->create(['role' => 'student']);
        Application::factory()->count(2)->create(['user_id' => $otherStudent->id]);

        $viewData = $this->getStudentProfileShowViewData();
        $this->assertEquals(0, $viewData['myApplicationsCount']);
    }

    // Tests for "Jobs in My Preferred Areas"
    public function test_jobs_in_my_preferred_areas_count_is_correct()
    {
        $preferredArea1 = AreaOfInterest::factory()->create();
        $preferredArea2 = AreaOfInterest::factory()->create();
        $otherArea = AreaOfInterest::factory()->create();

        $this->studentUser->areasOfInterest()->attach([$preferredArea1->id, $preferredArea2->id]);

        // Active jobs in preferred areas
        Job::factory()->create(['area_of_interest_id' => $preferredArea1->id, 'expiration_date' => Carbon::now()->addDays(5)]);
        Job::factory()->create(['area_of_interest_id' => $preferredArea2->id, 'expiration_date' => null]); // Active, null expiry

        // Active job in another area (control)
        Job::factory()->create(['area_of_interest_id' => $otherArea->id, 'expiration_date' => Carbon::now()->addDays(5)]);

        // Inactive job in preferred area (control)
        Job::factory()->create(['area_of_interest_id' => $preferredArea1->id, 'expiration_date' => Carbon::now()->subDays(5)]);

        $viewData = $this->getStudentProfileShowViewData();
        $this->assertEquals(2, $viewData['jobsInPreferredAreasCount']);
    }

    public function test_jobs_in_my_preferred_areas_count_is_zero_when_no_preferred_areas_set()
    {
        $area1 = AreaOfInterest::factory()->create();
        Job::factory()->create(['area_of_interest_id' => $area1->id, 'expiration_date' => Carbon::now()->addDays(5)]);

        $viewData = $this->getStudentProfileShowViewData();
        $this->assertEquals(0, $viewData['jobsInPreferredAreasCount']);
    }

    public function test_jobs_in_my_preferred_areas_count_is_zero_when_no_matching_active_jobs()
    {
        $preferredArea1 = AreaOfInterest::factory()->create();
        $otherArea = AreaOfInterest::factory()->create();
        $this->studentUser->areasOfInterest()->attach($preferredArea1->id);

        // Inactive job in preferred area
        Job::factory()->create(['area_of_interest_id' => $preferredArea1->id, 'expiration_date' => Carbon::now()->subDays(5)]);
        // Active job in another area
        Job::factory()->create(['area_of_interest_id' => $otherArea->id, 'expiration_date' => Carbon::now()->addDays(5)]);

        $viewData = $this->getStudentProfileShowViewData();
        $this->assertEquals(0, $viewData['jobsInPreferredAreasCount']);
    }

    public function test_jobs_in_my_preferred_areas_count_is_zero_when_no_jobs_exist_at_all()
    {
        $preferredArea1 = AreaOfInterest::factory()->create();
        $this->studentUser->areasOfInterest()->attach($preferredArea1->id);

        $viewData = $this->getStudentProfileShowViewData();
        $this->assertEquals(0, $viewData['jobsInPreferredAreasCount']);
    }
}
