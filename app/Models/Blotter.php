<?php
// app/Models/Blotter.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Blotter extends Model
{
    protected $fillable = [
        'blotter_no',
        'incident_date',
        'incident_type',
        'incident_location',
        'narrative',
        'status',
        'remarks',
        'complainant_resident_id',
        'respondent_resident_id',
        'complainant_name',
        'respondent_name',
        'complainant_contact',
        'respondent_contact',
        'recorded_by',
    ];

    protected $casts = [
        'incident_date' => 'datetime',
    ];

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function complainantResident(): BelongsTo
    {
        return $this->belongsTo(Resident::class, 'complainant_resident_id');
    }

    public function respondentResident(): BelongsTo
    {
        return $this->belongsTo(Resident::class, 'respondent_resident_id');
    }
}
