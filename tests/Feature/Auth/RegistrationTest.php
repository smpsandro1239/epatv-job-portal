<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

test('new users can register', function () {
    // Ensure an active registration window with a known password exists
    $windowPassword = 'test_window_password';
    \App\Models\RegistrationWindow::factory()->create([
        'is_active' => true,
        'start_time' => now()->subDay(),
        'end_time' => now()->addDay(),
        'password' => \Illuminate\Support\Facades\Hash::make($windowPassword),
        'max_registrations' => 10,
        'current_registrations' => 0,
    ]);

    $response = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'student',
        'window_password' => $windowPassword, // Provide the window password
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'role' => 'student',
    ]);
});
