<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('new users can register', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'student',
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'role' => 'student',
    ]);
});
