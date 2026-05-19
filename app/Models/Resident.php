<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resident extends Model
{
    protected $fillable = [
        'first_name','middle_name','last_name','sex','birth_date',
        'street','phase','address_line','barangay','city','province',
        'contact_no','email','civil_status','occupation','household_id',
        'account_no','user_id','verification_type','verification_id',
        'photo_path','id_image_path','selfie_image_path','child_doc_path',
        'guardian_full_name','guardian_contact_no','guardian_relationship',
        'guardian_email','registered_via','verification_status',
        'verified_at','verified_by','status','archived_at',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'verified_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    // Relationships
    public function user() { return $this->belongsTo(\App\Models\User::class); }
    public function documentRequests() { return $this->hasMany(\App\Models\DocumentRequest::class); }
    public function household() { return $this->belongsTo(\App\Models\Household::class); }
    public function householdMemberships() { return $this->hasMany(\App\Models\HouseholdMember::class); }

    // Archive Management
    public function archive() { $this->archived_at = now(); $this->save(); }
    public function restore() { $this->archived_at = null; $this->save(); }
    public function isArchived(): bool { return $this->archived_at !== null; }
    public function scopeArchived($query) { return $query->whereNotNull('archived_at'); }
    public function scopeNotArchived($query) { return $query->whereNull('archived_at'); }

    // Accessors
    public function getFullNameAttribute(): string {
        return trim($this->first_name.' '.($this->middle_name ? $this->middle_name.' ' : '').$this->last_name);
    }
    public function getAgeAttribute(): ?int {
        return $this->birth_date ? $this->birth_date->age : null;
    }
    public function getEmailAttribute(): ?string {
        return $this->user?->email;
    }

    // Helpers
    public function isVerified(): bool {
        return $this->verification_status === 'auto_verified';
    }

    public static function generateAccountNo(): string {
        $year = date('Y'); $month = date('m'); $day = date('d');
        $latest = self::where('account_no', 'like', "R{$year}{$month}{$day}-%")
            ->orderByDesc('account_no')->first();
        $sequence = 1;
        if ($latest && preg_match("/R{$year}{$month}{$day}-(\d{4})/", $latest->account_no, $matches)) {
            $sequence = intval($matches[1]) + 1;
        }
        return sprintf('R%s%s%s-%04d', $year, $month, $day, $sequence);
    }
}
