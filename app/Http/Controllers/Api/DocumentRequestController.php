<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\Resident;
use Illuminate\Http\Request;

class DocumentRequestController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');

        $items = DocumentRequest::query()
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

        return response()->json([
            'document_requests' => $items,
        ]);
    }

    public function show(DocumentRequest $documentRequest)
    {
        $documentRequest->load('resident','documentType');

        return response()->json([
            'document_request' => $documentRequest,
        ]);
    }

    public function residentOptions()
    {
        $residents = Resident::query()
            ->orderBy('last_name')
            ->get(['id','first_name','middle_name','last_name','address_line']);

        return response()->json(['residents' => $residents]);
    }

    public function documentTypeOptions()
    {
        $types = DocumentType::query()
            ->where('status','active')
            ->orderBy('name')
            ->get(['id','name','fee']);

        return response()->json(['document_types' => $types]);
    }
}
