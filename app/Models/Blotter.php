<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blotter extends Model
{
    protected $fillable = [
        'blotter_no',
        'incident_date',
        'incident_type',
        'incident_location',
        'narrative',
        'remarks',

        'complainant_resident_id',
        'respondent_resident_id',
        'complainant_name',
        'respondent_name',
        'complainant_contact',
        'respondent_contact',
        'complainant_email',
        'respondent_email',
        'recorded_by',
        'archived_at',
    ];

    protected $casts = [
        'incident_date' => 'datetime',
        'archived_at' => 'datetime',
    ];

    

    public function case()
    {
        return $this->hasOne(CaseFile::class, 'blotter_id');
    }

    public function complainantResident()
    {
        return $this->belongsTo(Resident::class, 'complainant_resident_id');
    }

    public function respondentResident()
    {
        return $this->belongsTo(Resident::class, 'respondent_resident_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Archive the blotter.
     */
    public function archive()
    {
        $this->archived_at = now();
        $this->save();
    }

    /**
     * Restore the blotter from archive.
     */
    public function restore()
    {
        $this->archived_at = null;
        $this->save();
    }

    /**
     * Check if the blotter is archived.
     */
    public function isArchived()
    {
        return $this->archived_at !== null;
    }

    /**
     * Scope to filter only archived blotters.
     */
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    /**
     * Scope to filter only non-archived blotters.
     */
    public function scopeNotArchived($query)
    {
        return $query->whereNull('archived_at');
    }
}
