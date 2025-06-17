<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\AreaOfInterest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth; // For API token generation

class StudentProfileTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        // It's good practice to ensure storage faking for tests involving file uploads
        Storage::fake('public');
    }

    private function createStudent(array $attributes = []): User
    {
        return User::factory()->create(array_merge(['role' => 'student'], $attributes));
    }

    private function createEmployer(array $attributes = []): User
    {
        return User::factory()->create(array_merge(['role' => 'employer'], $attributes));
    }

    // ====== API Tests ======

    public function test_student_can_view_own_profile_api()
    {
        $student = $this->createStudent();
        $token = JWTAuth::fromUser($student);

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->getJson('/api/student/profile');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'id', 'name', 'email', 'phone', 'course_completion_year',
                     'areas_of_interest', 'photo_url', 'cv_url'
                 ])
                 ->assertJson(['email' => $student->email]);
    }

    public function test_student_cannot_view_profile_without_authentication_api()
    {
        $response = $this->getJson('/api/student/profile');
        $response->assertStatus(401); // Expecting JWT middleware to deny
    }

    public function test_non_student_cannot_access_student_profile_api()
    {
        $employer = $this->createEmployer();
        $token = JWTAuth::fromUser($employer);

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->getJson('/api/student/profile');
        $response->assertStatus(403); // Expecting role middleware to deny
    }

    public function test_student_can_update_own_profile_api()
    {
        $student = $this->createStudent();
        $token = JWTAuth::fromUser($student);

        $updateData = [
            'name' => 'Updated Name',
            'phone' => '1234567890',
            'course_completion_year' => 2025,
        ];

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->putJson('/api/student/profile', $updateData);

        $response->assertStatus(200)
                 ->assertJsonPath('data.name', 'Updated Name')
                 ->assertJsonPath('data.phone', '1234567890');

        $this->assertDatabaseHas('users', [
            'id' => $student->id,
            'name' => 'Updated Name',
            'phone' => '1234567890',
        ]);
    }

    public function test_student_can_upload_photo_and_cv_api()
    {
        $student = $this->createStudent();
        $token = JWTAuth::fromUser($student);

        $photoFile = UploadedFile::fake()->image('new_photo.jpg');
        $cvFile = UploadedFile::fake()->create('new_cv.pdf', 100);

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->postJson('/api/student/profile', [ // Using postJson which is common for file uploads or ensure client sends PUT correctly
                             '_method' => 'PUT', // If your client sends PUT as POST with _method
                             'photo' => $photoFile,
                             'cv' => $cvFile,
                         ]);
        // If your test client handles PUT with files directly, use putJson.
        // Some HTTP clients or configurations might need POST with _method for multipart/form-data.
        // Laravel's test client should handle putJson with files correctly.
        // Let's try with putJson directly assuming it works as expected.

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->putJson('/api/student/profile', [
                             'photo' => $photoFile,
                             'cv' => $cvFile,
                         ]);


        $response->assertStatus(200);
        $student->refresh();

        $this->assertNotNull($student->photo);
        $this->assertNotNull($student->cv);
        Storage::disk('public')->assertExists($student->photo);
        Storage::disk('public')->assertExists($student->cv);

        // Clean up fake files (optional, as fake storage is used)
        // Storage::disk('public')->delete($student->photo);
        // Storage::disk('public')->delete($student->cv);
    }

    public function test_student_can_sync_areas_of_interest_api()
    {
        $student = $this->createStudent();
        $token = JWTAuth::fromUser($student);
        $areas = AreaOfInterest::factory()->count(3)->create();
        $areaIds = $areas->pluck('id')->toArray();

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->putJson('/api/student/profile', [
                             'areas_of_interest' => $areaIds,
                         ]);

        $response->assertStatus(200);
        $this->assertCount(3, $student->fresh()->areasOfInterest);
        foreach($areaIds as $areaId) {
            $this->assertDatabaseHas('user_areas_of_interest', [
                'user_id' => $student->id,
                'area_of_interest_id' => $areaId,
            ]);
        }

        // Test un-syncing
         $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->putJson('/api/student/profile', [
                             'areas_of_interest' => [$areaIds[0]], // Keep only one
                         ]);
        $response->assertStatus(200);
        $this->assertCount(1, $student->fresh()->areasOfInterest);
    }

    // ====== Web Tests ======

    public function test_student_can_view_own_profile_web()
    {
        $student = $this->createStudent();
        $response = $this->actingAs($student)->get('/student/profile');

        $response->assertStatus(200)
                 ->assertViewIs('student.profile.show')
                 ->assertViewHas('user', $student)
                 ->assertSee($student->email);
    }

    public function test_guest_cannot_view_student_profile_web()
    {
        $response = $this->get('/student/profile');
        $response->assertRedirect('/login');
    }

    public function test_non_student_cannot_access_student_profile_web()
    {
        $employer = $this->createEmployer();
        $response = $this->actingAs($employer)->get('/student/profile');
        $response->assertStatus(403);
    }

    public function test_student_can_view_edit_profile_form_web()
    {
        $student = $this->createStudent();
        $response = $this->actingAs($student)->get('/student/profile/edit');

        $response->assertStatus(200)
                 ->assertViewIs('student.profile.edit')
                 ->assertViewHas('user', $student)
                 ->assertViewHas('allAreasOfInterest');
    }

    public function test_student_can_update_profile_web()
    {
        $student = $this->createStudent();
        AreaOfInterest::factory()->count(2)->create(); // Ensure some areas exist

        $updateData = [
            'name' => 'Web Updated Name',
            'phone' => '0987654321',
            'course_completion_year' => 2023,
            'areas_of_interest' => AreaOfInterest::inRandomOrder()->limit(1)->pluck('id')->toArray(),
        ];

        $response = $this->actingAs($student)->put('/student/profile', $updateData);

        $response->assertRedirect('/student/profile')
                 ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $student->id,
            'name' => 'Web Updated Name',
            'phone' => '0987654321',
        ]);
        $this->assertCount(1, $student->fresh()->areasOfInterest);

        // Follow redirect and check if data is displayed
        $response = $this->actingAs($student)->get('/student/profile');
        $response->assertSee('Web Updated Name');
    }

    public function test_student_can_update_profile_with_photo_and_cv_web()
    {
        $student = $this->createStudent();
        $photoFile = UploadedFile::fake()->image('web_photo.jpg');
        $cvFile = UploadedFile::fake()->create('web_cv.pdf', 100);

        $updateData = [
            'name' => 'Web File Update Name',
            'photo' => $photoFile,
            'cv' => $cvFile,
        ];

        $response = $this->actingAs($student)->put('/student/profile', $updateData);
        $response->assertRedirect('/student/profile')->assertSessionHas('success');

        $student->refresh();
        $this->assertNotNull($student->photo);
        $this->assertNotNull($student->cv);
        Storage::disk('public')->assertExists($student->photo);
        Storage::disk('public')->assertExists($student->cv);
    }
}
