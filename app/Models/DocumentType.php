<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $fillable = [
        'name',
        'fee',
        'status',
        'template_path',
    ];

    public function requests()
    {
        return $this->hasMany(DocumentRequest::class);
    }
}

