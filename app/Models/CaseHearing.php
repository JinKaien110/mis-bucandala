<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseHearing extends Model
{
    protected $fillable = [
        'case_id',
        'scheduled_at',
        'location',
        'status',
        'notes',
        'result',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function caseFile()
    {
        return $this->belongsTo(CaseFile::class, 'case_id');
    }
}
