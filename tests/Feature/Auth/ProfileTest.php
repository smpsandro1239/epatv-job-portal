<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/profile');
        $response->assertStatus(200);
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->patch('/profile', [
            'name' => 'Test User Updated',
            'email' => 'testupdated@example.com',
        ]);

        // In the controller, redirect is to route('profile.edit')
        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHasNoErrors();
        $user->refresh();
        $this->assertSame('Test User Updated', $user->name);
        $this->assertSame('testupdated@example.com', $user->email);
        // Email verification status check after email update
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $response = $this->actingAs($user)->patch('/profile', [
            'name' => 'Test User Stays Verified',
            'email' => $user->email, // Email is not changed
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHasNoErrors();
        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_password_can_be_updated(): void
    {
        $user = User::factory()->create(['password' => Hash::make('current-password')]);
        $response = $this->actingAs($user)->put(route('profile.password.update'), [ // Using named route
            'current_password' => 'current-password',
            'password' => 'new-awesome-password',
            'password_confirmation' => 'new-awesome-password',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('new-awesome-password', $user->refresh()->password));
    }

    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('current-password')]);
        $response = $this->actingAs($user)->put(route('profile.password.update'), [ // Using named route
            'current_password' => 'wrong-current-password',
            'password' => 'new-awesome-password',
            'password_confirmation' => 'new-awesome-password',
        ]);

        // The controller uses the default error bag for password updates.
        $response->assertSessionHasErrors('current_password');
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password')]);
        $response = $this->actingAs($user)->delete(route('profile.destroy'), [ // Using named route
            'password' => 'password',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasNoErrors(); // If deletion is successful, no specific errors.
        // $this->app['auth']->logout(); // Removed this line
        $this->assertGuest('web'); // Specify the guard
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->delete(route('profile.destroy'), [ // Using named route
            'password' => 'wrong-password',
        ]);

        // The controller uses the default error bag for account deletion password validation.
        $response->assertSessionHasErrors('password');
        $this->assertNotNull($user->fresh());
    }
}
