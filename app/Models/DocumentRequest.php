<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'released_at'
    ];

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }
}
