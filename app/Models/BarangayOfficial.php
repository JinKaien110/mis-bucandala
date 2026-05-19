<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangayOfficial extends Model
{
    use HasFactory;

    protected $table = 'barangay_officials';

    protected $fillable = [
        'name',
        'position',
        'committee',
        'contact_no',
        'email',
        'photo_path',
        'barangay_term_id',
        'notes',
        'archived_at',
    ];

    protected $casts = [
        'archived_at' => 'datetime',
    ];

    public function term()
    {
        return $this->belongsTo(BarangayTerm::class, 'barangay_term_id');
    }

    public function getTermLabelAttribute()
    {
        if ($this->term) {
            return $this->term->term_label;
        }
        return 'No Term Assigned';
    }

    // For backward compatibility
    public function getTermAttribute()
    {
        return $this->term_label;
    }

    public function getIsActiveAttribute()
    {
        return $this->term && $this->term->is_active && !$this->term->is_archived;
    }

    public function getIsArchivedAttribute()
    {
        return $this->term && $this->term->is_archived;
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->photo_path) {
            return asset('storage/' . $this->photo_path);
        }
        return null;
    }

    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->name);
        $initials = '';
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
            if (strlen($initials) >= 2) break;
        }
        return $initials;
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
