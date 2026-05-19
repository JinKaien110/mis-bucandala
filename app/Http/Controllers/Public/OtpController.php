<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\OtpVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OtpController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'purpose' => ['required', 'in:resident_registration,pet_registration'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        // basic cooldown (60s)
        $latest = OtpVerification::where('purpose', $data['purpose'])
            ->where('email', $data['email'])
            ->orderByDesc('id')
            ->first();

        if ($latest && $latest->last_sent_at) {
            $seconds = $latest->last_sent_at->diffInSeconds(now());

            if ($seconds < 60) {
                return response()->json([
                    'message' => 'Please wait before requesting another OTP.',
                    'retry_after_seconds' => 60 - $seconds,
                ], 429);
            }
        }

        $otp = (string) random_int(100000, 999999);

        $row = OtpVerification::create([
            'purpose' => $data['purpose'],
            'email' => $data['email'],
            'otp_hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes(5),
            'last_sent_at' => now(),
            'request_ip' => $request->ip(),
        ]);

        // Minimal mail (no fancy template yet)
        Mail::raw("Your OTP code is: {$otp}\nThis expires in 5 minutes.", function ($m) use ($data) {
            $m->to($data['email'])->subject('Your OTP Code');
        });

        return response()->json([
            'message' => 'OTP sent.',
            'otp_id' => $row->id, // needed for verify step
            'expires_at' => $row->expires_at->toISOString(),
        ]);
    }

    public function verify(Request $request)
    {
        $data = $request->validate([
            'otp_id' => ['required', 'integer', 'exists:otp_verifications,id'],
            'otp' => ['required', 'digits:6'],
        ]);

        $row = OtpVerification::findOrFail($data['otp_id']);

        if ($row->verified_at) {
            return response()->json(['message' => 'Already verified.']);
        }

        if (now()->greaterThan($row->expires_at)) {
            return response()->json(['message' => 'OTP expired.'], 422);
        }

        if ($row->attempts >= 5) {
            return response()->json(['message' => 'Too many attempts.'], 429);
        }

        $row->increment('attempts');

        if (! Hash::check($data['otp'], $row->otp_hash)) {
            return response()->json(['message' => 'Invalid OTP.'], 422);
        }

        // return a short-lived token (simple) for the next submit step
        $token = Str::random(40);

        $row->update([
            'verified_at' => now(),
            'verification_token' => $token,
        ]);

        return response()->json([
            'message' => 'OTP verified.',
            'verification_token' => $token,
        ]);
    }
}
