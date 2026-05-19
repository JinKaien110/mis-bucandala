<?php

// app/Models/Household.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Household extends Model
{
    protected $fillable = [
        'household_code',
        'address_line',
        'street',
        'phase',
        'household_type',
        'homeownership_type',
        'head_name',
        'head_resident_id',
        'archived_at',

        // Members Summary
        'total_members',
        'total_adults',
        'total_minors',
        'total_senior_citizens',
        'total_pwd',

        // Contact
        'contact_no',

        // Socio-Economic
        'monthly_income_range',
        'employment_status',
        'primary_income_source',
        'is_4ps_beneficiary',
        'is_indigent',

        // Health & Community
        'has_pregnant_member',
        'has_senior_citizen',
        'has_pwd',
        'has_chronic_illness',

        // Housing & Utilities
        'house_type',
        'has_electricity',
        'has_toilet',
        'has_bathroom',
        'has_kitchen',
        'has_garage',

        // Community
        'registered_pets_count',
        'barangay_program_participation',
        'disaster_risk_level',
    ];

    protected $casts = [
        'archived_at' => 'datetime',
        'is_4ps_beneficiary' => 'boolean',
        'is_indigent' => 'boolean',
        'has_pregnant_member' => 'boolean',
        'has_senior_citizen' => 'boolean',
        'has_pwd' => 'boolean',
        'has_toilet' => 'boolean',
        'has_bathroom' => 'boolean',
        'has_kitchen' => 'boolean',
        'has_garage' => 'boolean',
        'has_chronic_illness' => 'boolean',
        'has_electricity' => 'boolean',
        'total_members' => 'integer',
        'total_adults' => 'integer',
        'total_minors' => 'integer',
        'total_senior_citizens' => 'integer',
        'total_pwd' => 'integer',
        'registered_pets_count' => 'integer',
    ];

    /**
     * Get residents belonging to this household.
     */
    public function residents()
    {
        return $this->hasMany(Resident::class, 'household_id');
    }

    public function members()
    {
        return $this->hasMany(HouseholdMember::class);
    }

    /**
     * Get the head of this household.
     */
    public function head()
    {
        return $this->belongsTo(Resident::class, 'head_resident_id');
    }

    /**
     * Archive the household.
     */
    public function archive()
    {
        $this->archived_at = now();
        $this->save();
    }

    /**
     * Restore the household from archive.
     */
    public function restore()
    {
        $this->archived_at = null;
        $this->save();
    }

    /**
     * Check if the household is archived.
     */
    public function isArchived()
    {
        return $this->archived_at !== null;
    }

    /**
     * Scope to filter only archived households.
     */
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    /**
     * Scope to filter only non-archived households.
     */
    public function scopeNotArchived($query)
    {
        return $query->whereNull('archived_at');
    }
}
