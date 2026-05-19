<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    protected $fillable = [
        'purpose', 'email', 'phone', 'otp_hash', 'expires_at', 'verified_at',
        'attempts', 'last_sent_at', 'request_ip', 'verification_token',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'last_sent_at' => 'datetime',
    ];
}
