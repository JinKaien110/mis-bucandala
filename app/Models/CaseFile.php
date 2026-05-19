<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseFile extends Model
{
    protected $table = 'cases';

    protected $fillable = [
        'case_no',
        'blotter_id',
        'status',
        'opened_at',
        'closed_at',
        'resolution_summary',
        'handled_by',
        'archived_at',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    public function blotter()
    {
        return $this->belongsTo(Blotter::class);
    }

    public function hearings()
    {
        return $this->hasMany(CaseHearing::class, 'case_id')->orderBy('scheduled_at');
    }

    public function handledBy()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /**
     * Archive the case.
     */
    public function archive()
    {
        $this->archived_at = now();
        $this->save();
    }

    /**
     * Restore the case from archive.
     */
    public function restore()
    {
        $this->archived_at = null;
        $this->save();
    }

    /**
     * Check if the case is archived.
     */
    public function isArchived()
    {
        return $this->archived_at !== null;
    }

    /**
     * Scope to filter only archived cases.
     */
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    /**
     * Scope to filter only non-archived cases.
     */
    public function scopeNotArchived($query)
    {
        return $query->whereNull('archived_at');
    }
}
