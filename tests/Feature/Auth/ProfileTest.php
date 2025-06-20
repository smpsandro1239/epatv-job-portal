<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
// use Illuminate\Support\Facades\Event; // Only if asserting events
// use Illuminate\Auth\Events\Registered; // Or other relevant events

uses(TestCase::class, RefreshDatabase::class);

test('profile page is displayed', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get('/profile');
    $response->assertStatus(200);
});

test('profile information can be updated', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->patch('/profile', [
        'name' => 'Test User Updated',
        'email' => 'testupdated@example.com',
    ]);
    $response->assertSessionHasNoErrors();
    $response->assertRedirect('/profile');
    $user->refresh();
    $this->assertSame('Test User Updated', $user->name);
    $this->assertSame('testupdated@example.com', $user->email);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $response = $this->actingAs($user)->patch('/profile', [
        'name' => 'Test User Stays Verified',
        'email' => $user->email,
    ]);
    $response->assertSessionHasNoErrors();
    $response->assertRedirect('/profile');
    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('password can be updated', function () {
    $user = User::factory()->create(['password' => Hash::make('current-password')]);
    $response = $this->actingAs($user)->put('/password', [
        'current_password' => 'current-password',
        'password' => 'new-awesome-password',
        'password_confirmation' => 'new-awesome-password',
    ]);
    $response->assertSessionHasNoErrors();
    $response->assertRedirect('/profile');
    $this->assertTrue(Hash::check('new-awesome-password', $user->refresh()->password));
});

test('correct password must be provided to update password', function () {
    $user = User::factory()->create(['password' => Hash::make('current-password')]);
    $response = $this->actingAs($user)->put('/password', [
        'current_password' => 'wrong-current-password',
        'password' => 'new-awesome-password',
        'password_confirmation' => 'new-awesome-password',
    ]);
    $response->assertSessionHasErrorsIn('updatePassword', 'current_password');
});

test('user can delete their account', function () {
    $user = User::factory()->create(['password' => Hash::make('password')]); // Ensure password is known
    $response = $this->actingAs($user)->delete('/profile', [
        'password' => 'password',
    ]);
    $response->assertSessionHasNoErrors();
    $response->assertRedirect('/');
    $this->assertGuest();
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->delete('/profile', [
        'password' => 'wrong-password',
    ]);
    $response->assertSessionHasErrorsIn('userDeletion', 'password');
    $this->assertNotNull($user->fresh());
});
