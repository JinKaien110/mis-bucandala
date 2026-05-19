<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentRequest extends Model
{
    protected $fillable = [
        'control_no',
        'resident_id',
        'document_type_id',
        'purpose',
        'remarks',
        'fee_amount',
        'status',
        'requested_by',
        'requested_via',
        'released_by',
        'released_at',
        'archived_at',
    ];

    protected $casts = [
        'released_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function cases()
    {
        return $this->hasMany(CaseHearing::class);
    }

    /**
     * Get the payment for this document request.
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Archive the document request.
     */
    public function archive()
    {
        $this->archived_at = now();
        $this->save();
    }

    /**
     * Restore the document request from archive.
     */
    public function restore()
    {
        $this->archived_at = null;
        $this->save();
    }

    /**
     * Check if the document request is archived.
     */
    public function isArchived()
    {
        return $this->archived_at !== null;
    }

    /**
     * Scope to filter only archived document requests.
     */
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    /**
     * Scope to filter only non-archived document requests.
     */
    public function scopeNotArchived($query)
    {
        return $query->whereNull('archived_at');
    }
}
