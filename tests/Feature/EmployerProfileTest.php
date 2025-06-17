<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class EmployerProfileTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    private function createEmployer(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'employer',
            'company_name' => $this->faker->company, // Ensure company_name is usually set for employers
        ], $attributes));
    }

    private function createStudent(array $attributes = []): User
    {
        return User::factory()->create(array_merge(['role' => 'student'], $attributes));
    }

    // ====== API Tests for Employer Registration ======

    public function test_employer_can_register_with_company_details_api()
    {
        $logoFile = UploadedFile::fake()->image('logo.png');
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'employer',
            'company_name' => $this->faker->company,
            'company_city' => $this->faker->city,
            'company_website' => $this->faker->url,
            'company_description' => $this->faker->paragraph,
            'company_logo' => $logoFile,
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(201)
                 ->assertJsonPath('user.email', $data['email'])
                 ->assertJsonPath('user.role', 'employer')
                 ->assertJsonPath('user.company_name', $data['company_name']);

        $this->assertDatabaseHas('users', ['email' => $data['email'], 'company_name' => $data['company_name']]);
        $user = User::where('email', $data['email'])->first();
        $this->assertNotNull($user->company_logo);
        Storage::disk('public')->assertExists($user->company_logo);
    }

    public function test_employer_registration_requires_company_name_api()
    {
        $data = [ /* missing company_name */
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'employer',
        ];
        $response = $this->postJson('/api/register', $data);
        $response->assertStatus(422)->assertJsonValidationErrors('company_name');
    }

    public function test_employer_registration_validates_company_website_api()
    {
        $data = [ /* ... other valid data ... */
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'employer',
            'company_name' => $this->faker->company,
            'company_website' => 'not-a-url',
        ];
        $response = $this->postJson('/api/register', $data);
        $response->assertStatus(422)->assertJsonValidationErrors('company_website');
    }

    public function test_employer_registration_validates_company_logo_api()
    {
        $textFile = UploadedFile::fake()->create('document.txt', 100); // Not an image
        $data = [ /* ... other valid data ... */
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'employer',
            'company_name' => $this->faker->company,
            'company_logo' => $textFile,
        ];
        $response = $this->postJson('/api/register', $data);
        $response->assertStatus(422)->assertJsonValidationErrors('company_logo');
    }

    // ====== API Tests for Employer Profile ======

    public function test_employer_can_view_own_profile_api()
    {
        $employer = $this->createEmployer();
        $token = JWTAuth::fromUser($employer);

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->getJson('/api/employer/profile');
        $response->assertStatus(200)
                 ->assertJsonPath('company_name', $employer->company_name)
                 ->assertJsonStructure(['id', 'name', 'email', 'company_name', 'company_logo_url']);
    }

    public function test_employer_can_update_own_profile_api()
    {
        $employer = $this->createEmployer();
        $token = JWTAuth::fromUser($employer);
        $newLogo = UploadedFile::fake()->image('new_logo.png');

        $updateData = [
            'company_name' => 'New Tech Inc.',
            'company_city' => 'New City',
            'company_logo' => $newLogo,
        ];

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->putJson('/api/employer/profile', $updateData); // putJson for API, or post with _method for some clients

        $response->assertStatus(200)
                 ->assertJsonPath('data.company_name', 'New Tech Inc.');

        $employer->refresh();
        $this->assertEquals('New Tech Inc.', $employer->company_name);
        $this->assertNotNull($employer->company_logo);
        Storage::disk('public')->assertExists($employer->company_logo);
    }

    public function test_employer_profile_api_authorization()
    {
        $this->getJson('/api/employer/profile')->assertStatus(401); // Unauthenticated

        $student = $this->createStudent();
        $token = JWTAuth::fromUser($student);
        $this->withHeaders(['Authorization' => "Bearer {$token}"])
             ->getJson('/api/employer/profile')->assertStatus(403); // Wrong role
    }

    // ====== Web Tests for Employer Profile ======

    public function test_employer_can_view_own_profile_web()
    {
        $employer = $this->createEmployer();
        $response = $this->actingAs($employer)->get('/employer/profile');
        $response->assertStatus(200)
                 ->assertViewIs('employer.profile.show')
                 ->assertSee($employer->company_name);
    }

    public function test_employer_can_view_edit_profile_form_web()
    {
        $employer = $this->createEmployer();
        $response = $this->actingAs($employer)->get('/employer/profile/edit');
        $response->assertStatus(200)->assertViewIs('employer.profile.edit');
    }

    public function test_employer_can_update_profile_web()
    {
        $employer = $this->createEmployer();
        $newLogo = UploadedFile::fake()->image('web_logo.png');
        $updateData = [
            'name' => 'Updated Contact',
            'company_name' => 'Web Corp Inc.',
            'company_logo' => $newLogo,
        ];

        $response = $this->actingAs($employer)->put('/employer/profile', $updateData);
        $response->assertRedirect('/employer/profile')
                 ->assertSessionHas('success');

        $employer->refresh();
        $this->assertEquals('Updated Contact', $employer->name);
        $this->assertEquals('Web Corp Inc.', $employer->company_name);
        $this->assertNotNull($employer->company_logo);
        Storage::disk('public')->assertExists($employer->company_logo);
    }

    public function test_employer_can_remove_logo_web()
    {
        $employer = $this->createEmployer(['company_logo' => UploadedFile::fake()->image('old.png')->store('public/company_logos')]);
        $this->assertNotNull($employer->company_logo);

        $updateData = [
            'name' => $employer->name, // Name is required by User model, ensure it's passed
            'company_name' => $employer->company_name, // Ensure company name is passed
            'remove_company_logo' => '1', // Checkbox to remove logo
        ];

        $response = $this->actingAs($employer)->put('/employer/profile', $updateData);
        $response->assertRedirect('/employer/profile')->assertSessionHas('success');

        $employer->refresh();
        $this->assertNull($employer->company_logo);
        // Add Storage::assertMissing if you want to ensure the old file was deleted.
    }


    public function test_employer_profile_web_authorization()
    {
        $this->get('/employer/profile')->assertRedirect('/login'); // Unauthenticated

        $student = $this->createStudent();
        $this->actingAs($student)->get('/employer/profile')->assertStatus(403); // Wrong role
    }
}
