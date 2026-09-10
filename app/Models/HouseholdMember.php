<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HouseholdMember extends Model
{
    protected $fillable = [
        'household_id',
        'resident_id',
        'first_name',
        'last_name',
        'email',
        'birth_date',
        'relationship',
        'is_pwd',
        'is_4ps_beneficiary',
        'is_indigent',
        'has_pregnant_member',
        'has_senior_citizen',
        'has_chronic_illness',
    ];

    protected $casts = [
        'is_pwd'              => 'boolean',
        'is_4ps_beneficiary'  => 'boolean',
        'is_indigent'         => 'boolean',
        'has_pregnant_member' => 'boolean',
        'has_senior_citizen'  => 'boolean',
        'has_chronic_illness' => 'boolean',
    ];

    /**
     * Get the household this member belongs to.
     */
    public function household()
    {
        return $this->belongsTo(Household::class);
    }

    /**
     * Get the resident associated with this household member.
     */
    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }
}
