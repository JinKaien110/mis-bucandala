<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class DocumentRequestController extends Controller
{
    public function index()
    {
        return view('admin.document-management.request');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'resident_id' => ['required','exists:residents,id'],
            'document_type_id' => ['required','exists:document_types,id'],
            'purpose' => ['nullable','string','max:255'],
            'remarks' => ['nullable','string','max:255'],
        ]);

        $docType = DocumentType::findOrFail($data['document_type_id']);

        // ✅ server-side control_no (safe unique)
        $controlNo = $this->generateControlNo();

        $req = DocumentRequest::create([
            'control_no' => $controlNo,
            'resident_id' => $data['resident_id'],
            'document_type_id' => $data['document_type_id'],
            'fee' => $docType->fee ?? 0,
            'purpose' => $data['purpose'] ?? null,
            'remarks' => $data['remarks'] ?? null,
            'status' => 'pending', // pending/approved/released/cancelled (you can adjust)
        ]);

        return back()->with('success', "Document request created. Control No: {$req->control_no}");
    }

    public function update(Request $request, DocumentRequest $documentRequest)
    {
        $data = $request->validate([
            'purpose' => ['nullable','string','max:255'],
            'remarks' => ['nullable','string','max:255'],
            'status' => ['required','in:pending,approved,released,cancelled'],
        ]);

        $documentRequest->update($data);

        return back()->with('success', "Document request updated (Control No: {$documentRequest->control_no}).");
    }

    public function toggle(DocumentRequest $documentRequest)
    {
        // simple toggle example (pending <-> cancelled)
        $documentRequest->status = $documentRequest->status === 'cancelled' ? 'pending' : 'cancelled';
        $documentRequest->save();

        return back()->with('success', "Request status updated (Control No: {$documentRequest->control_no}).");
    }

    private function generateControlNo(): string
    {
        // Example: BRGY-2026-000001
        $year = now()->format('Y');

        // You can improve this later; for now it's OK + unique index will protect it
        $last = DocumentRequest::whereYear('created_at', $year)->orderBy('id','desc')->value('id') ?? 0;
        $seq = str_pad((string)($last + 1), 6, '0', STR_PAD_LEFT);

        return "BRGY-{$year}-{$seq}";
    }

    public function apiIndex(Request $request)
{
    $q = $request->query('q');

    $items = \App\Models\DocumentRequest::query()
        ->with([
            'resident:id,first_name,middle_name,last_name,address_line',
            'documentType:id,name,fee'
        ])
        ->when($q, function ($query) use ($q) {
            $query->where('control_no', 'like', "%{$q}%")
                ->orWhereHas('resident', function ($qq) use ($q) {
                    $qq->where('first_name','like',"%{$q}%")
                       ->orWhere('last_name','like',"%{$q}%")
                       ->orWhere('address_line','like',"%{$q}%");
                })
                ->orWhereHas('documentType', function ($qq) use ($q) {
                    $qq->where('name','like',"%{$q}%");
                });
        })
        ->orderBy('id','desc')
        ->get();

    return response()->json(['document_requests' => $items]);
}

public function apiShow(\App\Models\DocumentRequest $documentRequest)
{
    $documentRequest->load('resident','documentType');
    return response()->json(['document_request' => $documentRequest]);
}

public function residentOptions()
{
    $residents = \App\Models\Resident::query()
        ->orderBy('last_name')
        ->get(['id','first_name','middle_name','last_name','address_line']);

    return response()->json(['residents' => $residents]);
}

public function documentTypeOptions()
{
    $types = \App\Models\DocumentType::query()
        ->where('status','active')
        ->orderBy('name')
        ->get(['id','name','fee']);

    return response()->json(['document_types' => $types]);
}

}
