<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class SuperadminUserManagementTest extends TestCase
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
        return User::factory()->create(array_merge(['role' => 'employer'], $attributes));
    }

    // ====== API Tests ======

    public function test_superadmin_can_list_users_api()
    {
        $superadmin = $this->createSuperadmin();
        $this->createStudent(['name' => 'Pending Student', 'registration_status' => 'pending']);
        $this->createEmployer(['name' => 'Approved Employer', 'registration_status' => 'approved']);
        $this->createSuperadmin(['email' => 'another.super@example.com']); // Should not be listed by default

        $token = JWTAuth::fromUser($superadmin);
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->getJson('/api/admin/users');

        $response->assertStatus(200)
                 ->assertJsonCount(2, 'data') // Pending Student + Approved Employer
                 ->assertJsonPath('data.0.name', 'Approved Employer') // latest first
                 ->assertJsonPath('data.1.name', 'Pending Student');
    }

    public function test_superadmin_can_filter_users_by_role_api()
    {
        $superadmin = $this->createSuperadmin();
        $this->createStudent(['registration_status' => 'approved']);
        $this->createEmployer(['registration_status' => 'approved']);

        $token = JWTAuth::fromUser($superadmin);
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->getJson('/api/admin/users?role=student');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonPath('data.0.role', 'student');
    }

    public function test_superadmin_can_filter_users_by_status_api()
    {
        $superadmin = $this->createSuperadmin();
        $this->createStudent(['registration_status' => 'pending']);
        $this->createEmployer(['registration_status' => 'approved']);

        $token = JWTAuth::fromUser($superadmin);
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->getJson('/api/admin/users?registration_status=pending');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonPath('data.0.registration_status', 'pending');
    }

    public function test_superadmin_can_approve_pending_user_api()
    {
        $superadmin = $this->createSuperadmin();
        $pendingStudent = $this->createStudent(['registration_status' => 'pending']);
        $token = JWTAuth::fromUser($superadmin);

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->postJson("/api/admin/users/{$pendingStudent->id}/approve");

        $response->assertStatus(200)
                 ->assertJsonPath('user.registration_status', 'approved');
        $this->assertDatabaseHas('users', ['id' => $pendingStudent->id, 'registration_status' => 'approved']);
    }

    public function test_superadmin_cannot_approve_already_approved_user_api()
    {
        $superadmin = $this->createSuperadmin();
        $approvedStudent = $this->createStudent(['registration_status' => 'approved']);
        $token = JWTAuth::fromUser($superadmin);

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->postJson("/api/admin/users/{$approvedStudent->id}/approve");
        $response->assertStatus(400); // Or other appropriate error code
    }

    public function test_superadmin_user_management_api_authorization()
    {
        $this->getJson('/api/admin/users')->assertStatus(401); // Unauthenticated

        $student = $this->createStudent();
        $pendingStudent = $this->createStudent(['registration_status' => 'pending']);
        $token = JWTAuth::fromUser($student);

        $this->withHeaders(['Authorization' => "Bearer {$token}"])
             ->getJson('/api/admin/users')->assertStatus(403); // Wrong role for listing
        $this->withHeaders(['Authorization' => "Bearer {$token}"])
             ->postJson("/api/admin/users/{$pendingStudent->id}/approve")->assertStatus(403); // Wrong role for approving
    }

    // ====== Web Tests ======

    public function test_superadmin_can_list_users_web()
    {
        $superadmin = $this->createSuperadmin();
        $student = $this->createStudent(['name' => 'Test Student Web']);
        $this->createEmployer(['name' => 'Test Employer Web']);

        $response = $this->actingAs($superadmin)->get('/admin/users');

        $response->assertStatus(200)
                 ->assertViewIs('admin.users.index')
                 ->assertSee($student->name)
                 ->assertSee('Test Employer Web');
    }

    public function test_superadmin_can_filter_users_web()
    {
        $superadmin = $this->createSuperadmin();
        $studentPending = $this->createStudent(['registration_status' => 'pending']);
        $employerApproved = $this->createEmployer(['registration_status' => 'approved']);

        $response = $this->actingAs($superadmin)->get('/admin/users?role=student&registration_status=pending');
        $response->assertStatus(200)
                 ->assertSee($studentPending->name)
                 ->assertDontSee($employerApproved->name);
    }

    public function test_superadmin_can_approve_pending_user_web()
    {
        $superadmin = $this->createSuperadmin();
        $pendingStudent = $this->createStudent(['registration_status' => 'pending']);

        $response = $this->actingAs($superadmin)->post("/admin/users/{$pendingStudent->id}/approve");

        $response->assertRedirect('/admin/users')->assertSessionHas('success');
        $this->assertDatabaseHas('users', ['id' => $pendingStudent->id, 'registration_status' => 'approved']);
    }

    public function test_superadmin_cannot_approve_already_approved_user_web()
    {
        $superadmin = $this->createSuperadmin();
        $approvedStudent = $this->createStudent(['registration_status' => 'approved']);

        $response = $this->actingAs($superadmin)->post("/admin/users/{$approvedStudent->id}/approve");
        $response->assertRedirect('/admin/users')->assertSessionHas('error');
    }

    public function test_superadmin_user_management_web_authorization()
    {
        $this->get('/admin/users')->assertRedirect('/login'); // Unauthenticated

        $student = $this->createStudent(); // Non-superadmin
        $pendingUser = $this->createStudent(['registration_status' => 'pending']);

        $this->actingAs($student)->get('/admin/users')->assertStatus(403);
        $this->actingAs($student)->post("/admin/users/{$pendingUser->id}/approve")->assertStatus(403);
    }
}
