<?php

namespace Tests\Feature;

use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_code_is_sent_for_an_account_without_disclosing_account_existence(): void
    {
        User::create([
            'email' => 'resident@example.com',
            'password' => 'OldPassword1!',
        ]);

        Mail::shouldReceive('raw')->once();

        $knownAccountResponse = $this->postJson('/api/v1/auth/password/forgot', [
            'email' => 'resident@example.com',
        ]);
        $unknownAccountResponse = $this->postJson('/api/v1/auth/password/forgot', [
            'email' => 'unknown@example.com',
        ]);

        $knownAccountResponse->assertOk()->assertExactJson([
            'message' => 'If an account exists for this email, a password reset code has been sent.',
        ]);
        $unknownAccountResponse->assertOk()->assertExactJson([
            'message' => 'If an account exists for this email, a password reset code has been sent.',
        ]);
        $this->assertDatabaseHas('otp_verifications', [
            'purpose' => 'password_reset',
            'email' => 'resident@example.com',
        ]);
        $this->assertDatabaseMissing('otp_verifications', [
            'purpose' => 'password_reset',
            'email' => 'unknown@example.com',
        ]);
    }

    public function test_verified_reset_code_can_change_password_once(): void
    {
        $user = User::create([
            'email' => 'resident@example.com',
            'password' => 'OldPassword1!',
        ]);

        OtpVerification::create([
            'purpose' => 'password_reset',
            'email' => $user->email,
            'otp_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(5),
            'last_sent_at' => now(),
        ]);

        $verifyResponse = $this->postJson('/api/v1/auth/password/verify', [
            'email' => $user->email,
            'otp' => '123456',
        ]);

        $verifyResponse->assertOk()->assertJsonStructure(['verification_token']);
        $token = $verifyResponse->json('verification_token');

        $this->postJson('/api/v1/auth/password/verify', [
            'email' => $user->email,
            'otp' => '123456',
        ])->assertUnprocessable();

        $resetData = [
            'email' => $user->email,
            'verification_token' => $token,
            'password' => 'NewPassword2!',
            'password_confirmation' => 'NewPassword2!',
        ];

        $this->postJson('/api/v1/auth/password/reset', $resetData)->assertOk();
        $this->assertTrue(Hash::check('NewPassword2!', $user->fresh()->password));

        $this->postJson('/api/v1/auth/password/reset', $resetData)->assertUnprocessable();
    }

    public function test_reset_code_expires_and_limits_attempts(): void
    {
        $user = User::create([
            'email' => 'resident@example.com',
            'password' => 'OldPassword1!',
        ]);

        OtpVerification::create([
            'purpose' => 'password_reset',
            'email' => $user->email,
            'otp_hash' => Hash::make('123456'),
            'expires_at' => now()->subSecond(),
            'last_sent_at' => now()->subMinutes(6),
        ]);

        $this->postJson('/api/v1/auth/password/verify', [
            'email' => $user->email,
            'otp' => '123456',
        ])->assertUnprocessable();

        $verification = OtpVerification::where('email', $user->email)->first();
        $verification->update(['expires_at' => now()->addMinutes(5), 'attempts' => 5]);

        $this->postJson('/api/v1/auth/password/verify', [
            'email' => $user->email,
            'otp' => '123456',
        ])->assertStatus(429);
    }

    public function test_reset_requires_a_password_with_letters_mixed_case_number_and_symbol(): void
    {
        $user = User::create([
            'email' => 'resident@example.com',
            'password' => 'OldPassword1!',
        ]);

        OtpVerification::create([
            'purpose' => 'password_reset',
            'email' => $user->email,
            'otp_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(5),
            'verified_at' => now(),
            'verification_token' => Hash::make('valid-reset-token'),
        ]);

        foreach (['short1A!', 'lowercase1!', 'UPPERCASE1!', 'NoNumbers!', 'NoSymbols1'] as $password) {
            $this->postJson('/api/v1/auth/password/reset', [
                'email' => $user->email,
                'verification_token' => 'valid-reset-token',
                'password' => $password,
                'password_confirmation' => $password,
            ])->assertUnprocessable()->assertJsonValidationErrors('password');
        }

        $this->assertTrue(Hash::check('OldPassword1!', $user->fresh()->password));
    }
}
