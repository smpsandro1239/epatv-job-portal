<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

test('email can be verified', function () {
    Notification::fake();
    $user = User::factory()->create(['email_verified_at' => null]);

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)]
    );

    $response = $this->actingAs($user, 'api')->getJson($verificationUrl);

    $response->assertOk();
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    Notification::assertSentTo($user, VerifyEmail::class);
});

test('email verification fails with invalid hash', function () {
    $user = User::factory()->create(['email_verified_at' => null]);

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => 'invalid-hash']
    );

    $response = $this->actingAs($user, 'api')->getJson($verificationUrl);

    $response->assertStatus(400);
    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});
