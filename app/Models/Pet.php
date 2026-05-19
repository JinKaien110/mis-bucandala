<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'resident_id',
        'nickname',
        'species',
        'breed',
        'birth_date',
        'sex',
        'color',
        'photo_path',
        'vaccination_status',
        'notes',
        'archived_at',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'archived_at' => 'datetime',
    ];

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function getAgeAttribute()
    {
        if (! $this->birth_date) {
            return null;
        }

        $now = now();
        $birthDate = $this->birth_date;

        // If birth date is in the future, return 0
        if ($birthDate->gt($now)) {
            return '0 years';
        }

        $diff = $now->diff($birthDate);
        $years = $diff->y;
        $months = $diff->m;
        $days = $diff->d;

        // Return in most appropriate unit
        if ($years >= 1) {
            return $years.' year'.($years > 1 ? 's' : '');
        } elseif ($months >= 1) {
            return $months.' month'.($months > 1 ? 's' : '');
        } elseif ($days >= 1) {
            return $days.' day'.($days > 1 ? 's' : '');
        } else {
            return '0 days';
        }
    }

    /**
     * Archive the pet.
     */
    public function archive()
    {
        $this->archived_at = now();
        $this->save();
    }

    /**
     * Restore the pet from archive.
     */
    public function restore()
    {
        $this->archived_at = null;
        $this->save();
    }

    /**
     * Check if the pet is archived.
     */
    public function isArchived()
    {
        return $this->archived_at !== null;
    }

    /**
     * Scope to filter only archived pets.
     */
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    /**
     * Scope to filter only non-archived pets.
     */
    public function scopeNotArchived($query)
    {
        return $query->whereNull('archived_at');
    }
}
