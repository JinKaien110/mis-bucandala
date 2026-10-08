<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class PasswordResetController extends Controller
{
    private const PURPOSE = 'password_reset';

    public function send(Request $request)
    {
        
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if ($user) {
            $latest = OtpVerification::where('purpose', self::PURPOSE)
                ->where('email', $data['email'])
                ->orderByDesc('id')
                ->first();

            if (! $latest || ! $latest->last_sent_at || $latest->last_sent_at->diffInSeconds(now()) >= 60) {
                $otp = (string) random_int(100000, 999999);

                OtpVerification::create([
                    'purpose' => self::PURPOSE,
                    'email' => $data['email'],
                    'otp_hash' => Hash::make($otp),
                    'expires_at' => now()->addMinutes(5),
                    'last_sent_at' => now(),
                    'request_ip' => $request->ip(),
                ]);

                Mail::raw("Your password reset code is: {$otp}\nThis code expires in 5 minutes.", function ($message) use ($data) {
                    $message->to($data['email'])->subject('Your Password Reset Code');
                });
            }
        }

        return response()->json([
            'message' => 'If an account exists for this email, a password reset code has been sent.',
        ]);
    }

    public function verify(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'otp' => ['required', 'digits:6'],
        ]);

        $verification = OtpVerification::where('purpose', self::PURPOSE)
            ->where('email', $data['email'])
            ->orderByDesc('id')
            ->first();

        if (! $verification || $verification->verified_at || now()->greaterThan($verification->expires_at)) {
            return response()->json(['message' => 'Invalid or expired reset code.'], 422);
        }

        if ($verification->attempts >= 5) {
            return response()->json(['message' => 'Too many attempts. Request a new reset code.'], 429);
        }

        $verification->increment('attempts');

        if (! Hash::check($data['otp'], $verification->otp_hash)) {
            return response()->json(['message' => 'Invalid or expired reset code.'], 422);
        }

        $token = Str::random(64);

        $verification->update([
            'verified_at' => now(),
            'verification_token' => Hash::make($token),
        ]);

        return response()->json([
            'message' => 'Reset code verified.',
            'verification_token' => $token,
        ]);
    }

    public function reset(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'verification_token' => ['required', 'string', 'min:40'],
            'password' => [
                'required',
                'string',
                Password::min(8)->letters()->mixedCase()->numbers()->symbols(),
                'confirmed',
            ],
        ]);

        $updated = DB::transaction(function () use ($data) {
            $verification = OtpVerification::where('purpose', self::PURPOSE)
                ->where('email', $data['email'])
                ->whereNotNull('verified_at')
                ->where('expires_at', '>', now())
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            if (! $verification || ! $verification->verification_token
                || ! Hash::check($data['verification_token'], $verification->verification_token)) {
                return false;
            }

            $user = User::where('email', $data['email'])->lockForUpdate()->first();

            if (! $user) {
                return false;
            }

            $user->password = $data['password'];
            $user->setRememberToken(Str::random(60));
            $user->save();

            $verification->update(['verification_token' => null]);

            return true;
        });

        if (! $updated) {
            return response()->json(['message' => 'The reset request is invalid or expired. Please request a new code.'], 422);
        }

        return response()->json(['message' => 'Your password has been reset.']);
    }
}
