<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HouseholdJoinRequest extends Model
{
    protected $fillable = [
        'household_id',
        'resident_id',
        'status',
        'message',
        'responded_by',
        'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    /**
     * Get the household this join request belongs to.
     */
    public function household()
    {
        return $this->belongsTo(Household::class);
    }

    /**
     * Get the resident who created this request.
     */
    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    /**
     * Get the user who responded to this request.
     */
    public function responder()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }
}
