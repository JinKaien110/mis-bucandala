<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'start_datetime',
        'end_datetime',
        'location',
        'type',
        'is_all_day',
        'is_published',
        'reminder',
        'created_by',
        'archived_at',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'is_all_day' => 'boolean',
        'is_published' => 'boolean',
        'archived_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_datetime', '>=', now())->orderBy('start_datetime');
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
