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
    ];

    protected $casts = [
        'is_pwd' => 'boolean',
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
