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
            ->notArchived()
            ->when($q, fn($query) =>
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('description', 'like', "%{$q}%")
            )
            ->orderBy('id', 'desc')
            ->get(['id','name','description', 'template_path','fee','status','created_at', 'file_name']);

        return response()->json(['types' => $types]);
    }

    // Update the store method
public function store(Request $request)
{
    $data = $request->validate([
        'name' => ['required','string','max:255'],
        'fee' => ['nullable','numeric','min:0'],
        'file_name' => ['nullable','string','max:255'],
        'template' => ['nullable','file','mimes:docx','max:5120'], 
    ]);

    $doc = new DocumentType();
    $doc->name = $data['name'];
    $doc->fee = $data['fee'] ?? 0;
    $doc->status = 'active';

    if ($request->hasFile('template')) {
        $file = $request->file('template');
        
        // LOGIC: Use custom name if provided, else use actual original filename (without .docx)
        $baseName = !empty($data['file_name']) 
            ? $data['file_name'] 
            : pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $doc->file_name = $baseName; // Save the actual/custom name to DB
        $doc->template_path = $file->storeAs('document-templates', $baseName . '.docx', 'public');
    } else {
        $doc->file_name = $data['file_name'] ?? null;
    }

    $doc->save();
    return back()->with('success', 'Document type created.');
}

// Update the update method
public function update(Request $request, DocumentType $documentType)
{
    $data = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'fee' => ['nullable', 'numeric', 'min:0'],
        'file_name' => ['nullable', 'string', 'max:255'],
        'template' => ['nullable', 'file', 'mimes:docx', 'max:5120'],
    ]);

    $oldPath = $documentType->template_path;
    $newName = $data['file_name'];

    // 1. If a NEW file is uploaded
    if ($request->hasFile('template')) {
        // Delete the old file first
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        $file = $request->file('template');
        // If Name input is empty, use actual filename of the new upload
        $baseName = !empty($newName) ? $newName : pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        
        $documentType->file_name = $baseName;
        $documentType->template_path = $file->storeAs('document-templates', $baseName . '.docx', 'public');

    } 
    // 2. No new file, but the File Name field was changed (Renaming existing file)
    elseif (!empty($newName) && $newName !== $documentType->file_name && $oldPath) {
        $newPath = 'document-templates/' . $newName . '.docx';
        
        if (Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->move($oldPath, $newPath);
            $documentType->template_path = $newPath;
        }
        $documentType->file_name = $newName;
    }

    $documentType->name = $data['name'];
    $documentType->fee = $data['fee'] ?? 0;
    $documentType->save();

    return back()->with('success', 'Document type updated.');
}

    public function show(DocumentType $documentType)
    {
        return response()->json(['document_type' => $documentType]);
    }

     

    public function toggleStatus(DocumentType $documentType)
    {
        $documentType->status = $documentType->status === 'active' ? 'inactive' : 'active';
        $documentType->save();

        return back()->with('success', 'Status updated to ' . $documentType->status);
    }

    public function destroy(DocumentType $documentType)
    {
        $documentType->archive();
        
        if (request()->expectsJson()) {
            return response()->json(['message' => 'Document type archived.']);
        }
        return back()->with('success', 'Document type archived.');
    }

    public function restore(DocumentType $documentType)
    {
        $documentType->restore();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Document type restored.']);
        }
        return back()->with('success', 'Document type restored.');
    }
}
