<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;
// No Event or Notification faking/assertions needed for these specific instructions

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_can_be_verified(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        // Request without actingAs, expecting JSON response
        $response = $this->getJson($verificationUrl);

        // As per instruction, assertOk(). This implies the controller
        // should handle this scenario and return a 200 JSON response
        // if an unauthenticated user tries to verify with a valid link.
        // This is different from typical web flow which redirects.
        // The actual controller 'VerifyEmailController' redirects.
        // This test will likely fail if the route still has 'auth' middleware.
        $response->assertOk();
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_email_verification_fails_with_invalid_hash(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);

        $incorrectHash = sha1($user->getEmailForVerification() . 'tampered');

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => $incorrectHash]
        );

        // Request without actingAs, expecting JSON response
        $response = $this->getJson($verificationUrl);

        // Expecting 403 due to invalid signature from the 'signed' middleware
        $response->assertStatus(403);
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
