<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;


class DocumentTypeController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');

        $types = DocumentType::query()
            ->when($q, fn($query) =>
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('description', 'like', "%{$q}%")
            )
            ->orderBy('id', 'desc')
            ->get(['id','name','description', 'template_path','fee','status','created_at']);

        return response()->json(['types' => $types]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'fee' => ['nullable','numeric','min:0'],
            'template' => ['nullable','file','mimes:docx','max:5120'], // 5MB
        ]);

        $doc = new DocumentType();
        $doc->name = $data['name'];
        $doc->fee = $data['fee'] ?? 0;
        $doc->status = 'active';

        if ($request->hasFile('template')) {
            $doc->template_path = $request->file('template')
                ->store('document-templates', 'public');
        }

        $doc->save();

        return back()->with('success', 'Document type created.');
    }

    public function show(DocumentType $documentType)
    {
        return response()->json(['type' => $documentType]);
    }

     public function update(Request $request, DocumentType $documentType)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'fee' => ['nullable','numeric','min:0'],
            'status' => ['required', Rule::in(['active','inactive'])],
            'template' => ['nullable','file','mimes:docx','max:5120'],
        ]);

        $documentType->name = $data['name'];
        $documentType->fee = $data['fee'] ?? 0;
        $documentType->status = $data['status'];

        if ($request->hasFile('template')) {
            // delete old template if exists
            if ($documentType->template_path) {
                Storage::disk('public')->delete($documentType->template_path);
            }
            $documentType->template_path = $request->file('template')
                ->store('document-templates', 'public');
        }

        $documentType->save();

        return back()->with('success', 'Document type updated.');
    }

    public function toggleStatus(DocumentType $documentType)
    {
        $documentType->status = $documentType->status === 'active' ? 'inactive' : 'active';
        $documentType->save();

        return response()->json(['message' => 'Status updated', 'status' => $documentType->status]);
    }
}
