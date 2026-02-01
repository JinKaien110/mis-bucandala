<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resident extends Model
{
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'sex',
        'birth_date',
        'address_line',
        'barangay',
        'city',
        'province',
        'contact_no',
        'email',
        'civil_status',
        'occupation',
        'verification_id',
        'photo_path',
        'verification_type',
        'verification_status',
        'verified_at',
        'verified_by',
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'verified_at' => 'datetime',
    ];

    public function documentRequests() {
    return $this->hasMany(\App\Models\DocumentRequest::class);
}

}

