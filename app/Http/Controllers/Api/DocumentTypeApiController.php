<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use Illuminate\Http\Request;

class DocumentTypeApiController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');

        $docs = DocumentType::query()
            ->when($q, fn($query) =>
                $query->where('name', 'like', "%{$q}%")
            )
            ->orderBy('id', 'desc')
            ->get(['id','name','fee','status','template_path','created_at']);

        return response()->json(['document_types' => $docs]);
    }

    public function show(DocumentType $documentType)
    {
        return response()->json(['document_type' => $documentType]);
    }
}
