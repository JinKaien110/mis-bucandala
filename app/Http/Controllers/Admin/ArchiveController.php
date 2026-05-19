<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use App\Models\DocumentType;
use App\Models\DocumentRequest;
use Illuminate\Http\Request;

class ArchiveController extends Controller
{
    public function index()
    {
        return view('admin.archive.index');
    }

    public function residents()
    {
        $residents = Resident::query()
            ->archived()
            ->with(['user'])
            ->orderBy('archived_at', 'desc')
            ->get(['id', 'first_name', 'middle_name', 'last_name', 'birth_date', 'civil_status', 'address_line', 'status', 'archived_at', 'user_id']);

        return response()->json(['residents' => $residents]);
    }

    public function documentTypes()
    {
        $types = DocumentType::query()
            ->archived()
            ->orderBy('archived_at', 'desc')
            ->get(['id', 'name', 'description', 'fee', 'file_name', 'template_path', 'archived_at']);

        return response()->json(['document_types' => $types]);
    }

    public function documentRequests()
    {
        $requests = DocumentRequest::query()
            ->with(['resident', 'documentType'])
            ->archived() // only requests with archived_at set
            ->orderBy('archived_at', 'desc')
            ->get();

        return response()->json(['document_requests' => $requests]);
    }
}
