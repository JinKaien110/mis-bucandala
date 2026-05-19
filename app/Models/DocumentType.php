<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentType extends Model
{
    protected $fillable = [
        'name',
        'fee',
        'status',
        'template_path',
        'file_name',
        'archived_at',
    ];

    protected $casts = [
        'archived_at' => 'datetime',
    ];

    public function requests()
    {
        return $this->hasMany(DocumentRequest::class);
    }

    /**
     * Archive the document type.
     */
    public function archive()
    {
        $this->archived_at = now();
        $this->save();
    }

    /**
     * Restore the document type from archive.
     */
    public function restore()
    {
        $this->archived_at = null;
        $this->save();
    }

    /**
     * Check if the document type is archived.
     */
    public function isArchived()
    {
        return $this->archived_at !== null;
    }

    /**
     * Scope to filter only archived document types.
     */
    public function scopeArchived($query)
    {
        return $query->whereNotNull('archived_at');
    }

    /**
     * Scope to filter only non-archived document types.
     */
    public function scopeNotArchived($query)
    {
        return $query->whereNull('archived_at');
    }
}

