<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\RegistrationWindow;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;

class SuperadminRegistrationWindowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private function createSuperadmin(array $attributes = []): User
    {
        return User::factory()->create(array_merge(['role' => 'superadmin'], $attributes));
    }

    private function getOrCreateWindow(): RegistrationWindow
    {
        return RegistrationWindow::firstOrCreate([], [
            'start_time' => now()->addDay()->startOfHour()->toDateTimeString(),
            'end_time' => now()->addDays(2)->startOfHour()->toDateTimeString(),
            'max_registrations' => 100,
            'is_active' => false,
            'password' => null,
            'current_registrations' => 0,
            'first_use_time' => null,
        ]);
    }

    // ====== API Tests ======

    public function test_superadmin_can_get_registration_window_settings_api()
    {
        $superadmin = $this->createSuperadmin();
        $this->getOrCreateWindow(); // Ensure window exists
        $token = JWTAuth::fromUser($superadmin);

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->getJson('/api/admin/registration-window');

        $response->assertStatus(200)
                 ->assertJsonStructure(['id', 'start_time', 'end_time', 'max_registrations', 'is_active', 'current_registrations', 'first_use_time']);
    }

    public function test_superadmin_can_update_registration_window_settings_api()
    {
        $superadmin = $this->createSuperadmin();
        $window = $this->getOrCreateWindow();
        $token = JWTAuth::fromUser($superadmin);

        $newStartTime = now()->addHours(2)->startOfHour()->format('Y-m-d H:i:s');
        $newEndTime = now()->addDays(3)->startOfHour()->format('Y-m-d H:i:s');

        $updateData = [
            'start_time' => $newStartTime,
            'end_time' => $newEndTime,
            'max_registrations' => 50,
            'is_active' => true,
        ];

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->putJson('/api/admin/registration-window', $updateData);

        $response->assertStatus(200)
                 ->assertJsonPath('window.max_registrations', 50)
                 ->assertJsonPath('window.is_active', true);

        $window->refresh();
        $this->assertEquals(50, $window->max_registrations);
        $this->assertTrue($window->is_active);
        $this->assertEquals($newStartTime, Carbon::parse($window->start_time)->format('Y-m-d H:i:s'));
    }

    public function test_superadmin_updating_password_resets_counts_api()
    {
        $superadmin = $this->createSuperadmin();
        $window = $this->getOrCreateWindow();
        // Simulate prior use
        $window->update(['current_registrations' => 10, 'first_use_time' => now()->subHour(), 'password' => Hash::make('oldpassword')]);

        $token = JWTAuth::fromUser($superadmin);
        $updateData = ['password' => 'newStrongPassword123'];

        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->putJson('/api/admin/registration-window', $updateData);

        $response->assertStatus(200);
        $window->refresh();
        $this->assertTrue(Hash::check('newStrongPassword123', $window->password));
        $this->assertEquals(0, $window->current_registrations);
        $this->assertNull($window->first_use_time);
    }

    public function test_registration_window_validation_api()
    {
        $superadmin = $this->createSuperadmin();
        $token = JWTAuth::fromUser($superadmin);

        // End time before start time
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->putJson('/api/admin/registration-window', [
                             'start_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
                             'end_time' => now()->addDay()->format('Y-m-d H:i:s'),
                         ]);
        $response->assertStatus(422)->assertJsonValidationErrors('end_time');

        // Negative max_registrations
        $response = $this->withHeaders(['Authorization' => "Bearer {$token}"])
                         ->putJson('/api/admin/registration-window', ['max_registrations' => -5]);
        $response->assertStatus(422)->assertJsonValidationErrors('max_registrations');
    }

    public function test_registration_window_api_authorization()
    {
        $this->getJson('/api/admin/registration-window')->assertStatus(401); // Unauthenticated

        $student = User::factory()->create(['role' => 'student']);
        $token = JWTAuth::fromUser($student);
        $this->withHeaders(['Authorization' => "Bearer {$token}"])
             ->getJson('/api/admin/registration-window')->assertStatus(403); // Wrong role
    }

    // ====== Web Tests ======

    public function test_superadmin_can_view_edit_registration_window_form_web()
    {
        $superadmin = $this->createSuperadmin();
        $this->getOrCreateWindow(); // Ensure window exists

        $response = $this->actingAs($superadmin)->get('/admin/registration-window');
        $response->assertStatus(200)
                 ->assertViewIs('admin.registration_window.edit')
                 ->assertViewHas('window');
    }

    public function test_superadmin_can_update_registration_window_web()
    {
        $superadmin = $this->createSuperadmin();
        $window = $this->getOrCreateWindow();

        $newStartTime = Carbon::now()->addDays(1)->startOfHour();
        $newEndTime = Carbon::now()->addDays(3)->startOfHour();

        $updateData = [
            'start_time' => $newStartTime->format('Y-m-d\TH:i'),
            'end_time' => $newEndTime->format('Y-m-d\TH:i'),
            'max_registrations' => 75,
            'is_active' => '1', // Checkbox value when checked
        ];

        $response = $this->actingAs($superadmin)->put('/admin/registration-window', $updateData);

        $response->assertRedirect('/admin/registration-window') // Redirects back to edit page
                 ->assertSessionHas('success');

        $window->refresh();
        $this->assertEquals(75, $window->max_registrations);
        $this->assertTrue($window->is_active);
        $this->assertEquals($newStartTime->toDateTimeString(), $window->start_time->toDateTimeString());
    }

    public function test_superadmin_updating_password_resets_counts_web()
    {
        $superadmin = $this->createSuperadmin();
        $window = $this->getOrCreateWindow();
        $window->update(['current_registrations' => 5, 'first_use_time' => now()->subHours(1), 'password' => Hash::make('oldpass')]);

        $updateData = [
            'start_time' => Carbon::parse($window->start_time)->format('Y-m-d\TH:i'),
            'end_time' => Carbon::parse($window->end_time)->format('Y-m-d\TH:i'),
            'max_registrations' => $window->max_registrations,
            'password' => 'newWebPassword123',
            'password_confirmation' => 'newWebPassword123',
            // 'is_active' will be conditionally added below
        ];

        // Simulate checkbox behavior: only send 'is_active' if it's meant to be true.
        // The controller AdminController@updateRegistrationWindow uses $request->has('is_active').
        if ($window->is_active) { // Maintain current state for this test, or set to a specific state if needed
            $updateData['is_active'] = '1';
        }
        // If $window->is_active is false, 'is_active' will not be in $updateData.
        // Controller's $request->has('is_active') will correctly evaluate to false.


        $response = $this->actingAs($superadmin)->put('/admin/registration-window', $updateData);
        $response->assertRedirect('/admin/registration-window')->assertSessionHas('success');

        $window->refresh();
        $this->assertTrue(Hash::check('newWebPassword123', $window->password));
        $this->assertEquals(0, $window->current_registrations);
        $this->assertNull($window->first_use_time);
    }

    public function test_registration_window_web_authorization()
    {
        $this->get('/admin/registration-window')->assertRedirect('/login'); // Unauthenticated

        $student = User::factory()->create(['role' => 'student']);
        $this->actingAs($student)->get('/admin/registration-window')->assertStatus(403); // Wrong role
    }
}
