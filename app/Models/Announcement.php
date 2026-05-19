<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'content',
        'type',
        'publish_date',
        'expire_date',
        'is_published',
        'created_by',
        'event_id',
        'archived_at',
        'show_on_calendar',
    ];

    protected $casts = [
        'publish_date' => 'date',
        'expire_date' => 'date',
        'is_published' => 'boolean',
        'archived_at' => 'datetime',
        'show_on_calendar' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Archive the record
     */
    public function archive()
    {
        $this->archived_at = now();
        $this->save();
    }

    /**
     * Restore the record from archive
     */
    public function restore()
    {
        $this->archived_at = null;
        $this->save();
    }

    /**
     * Check if record is archived
     */
    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }

    /**
     * Scope to filter only archived records
     */
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    /**
     * Scope to filter only non-archived records
     */
    public function scopeNotArchived($query)
    {
        return $query->whereNull('archived_at');
    }
}
