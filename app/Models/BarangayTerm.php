<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangayTerm extends Model
{
    use HasFactory;

    protected $table = 'barangay_terms';

    protected $fillable = [
        'term_start',
        'term_end',
        'title',
        'is_active',
        'is_archived',
        'notes',
    ];

    protected $casts = [
        'term_start' => 'integer',
        'term_end' => 'integer',
        'is_active' => 'boolean',
        'is_archived' => 'boolean',
    ];

    public function officials()
    {
        return $this->hasMany(BarangayOfficial::class, 'barangay_term_id');
    }

    // For counting officials in views
    public function getOfficialsCountAttribute()
    {
        return $this->officials()->count();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('is_archived', false);
    }

    public function scopeArchived($query)
    {
        return $query->where('is_archived', true);
    }

    public function getTermLabelAttribute()
    {
        if ($this->title) {
            return $this->title;
        }
        
        if ($this->term_end) {
            return $this->term_start . ' - ' . $this->term_end;
        }
        return $this->term_start . ' - Present';
    }

    public function getIsCurrentAttribute()
    {
        $currentYear = date('Y');
        
        if ($this->is_archived) {
            return false;
        }
        
        if ($this->term_end) {
            return $currentYear >= $this->term_start && $currentYear <= $this->term_end;
        }
        
        return $currentYear >= $this->term_start;
    }
}
