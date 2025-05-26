<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

test('reset password link can be requested', function () {
    Notification::fake();
    $user = User::factory()->create();

    $response = $this->postJson('/api/forgot-password', ['email' => $user->email]);

    $response->assertOk();
    Notification::assertSentTo($user, ResetPassword::class);
});

test('password can be reset with valid token', function () {
    Event::fake();
    $user = User::factory()->create();

    $token = Password::createToken($user);

    $response = $this->postJson('/api/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ]);

    $response->assertOk();
    Event::assertDispatched(\Illuminate\Auth\Events\PasswordReset::class, function ($event) use ($user) {
        return $event->user->id === $user->id;
    });
});
