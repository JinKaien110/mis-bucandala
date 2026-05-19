<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use App\Models\Household;
use App\Models\Payment;
use App\Models\Blotter;
use App\Models\CaseFile;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total_residents' => Resident::count(),
            'total_households' => Household::count(),
            'total_blotters' => Blotter::count(),
            'total_cases' => CaseFile::count(),
            'total_document_requests' => DocumentRequest::count(),
            'total_collected' => Payment::where('status', 'paid')
                ->select(DB::raw('COALESCE(SUM(amount), 0) as total'))
                ->value('total') ?? 0,
        ];

        return view('admin.reports.index', compact('stats'));
    }

    public function residents(Request $request)
    {
        $validated = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        $q = Resident::query();

        if (!empty($validated['date_from'])) {
            $q->whereDate('created_at', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $q->whereDate('created_at', '<=', $validated['date_to']);
        }

        $residents = $q->orderByDesc('id')->paginate(20)->withQueryString();

        $demographics = [
            'total' => Resident::count(),
            'male' => Resident::where('gender', 'male')->count(),
            'female' => Resident::where('gender', 'female')->count(),
            'voters' => Resident::where('is_voter', true)->count(),
            'non_voters' => Resident::where('is_voter', false)->count(),
        ];

        $ageGroups = Resident::select('age')
            ->when(!empty($validated['date_from']), fn($q) => $q->whereDate('created_at', '>=', $validated['date_from']))
            ->when(!empty($validated['date_to']), fn($q) => $q->whereDate('created_at', '<=', $validated['date_to']))
            ->get()
            ->groupBy(fn($r) => match(true) {
                $r->age < 18 => 'Under 18',
                $r->age < 35 => '18-34',
                $r->age < 50 => '35-49',
                $r->age < 65 => '50-64',
                default => '65+',
            })
            ->map(fn($group) => $group->count());

        return view('admin.reports.residents', compact('residents', 'demographics', 'ageGroups'));
    }

    public function financial(Request $request)
    {
        $validated = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        $q = Payment::query()
            ->with(['documentRequest', 'resident', 'collector'])
            ->where('status', 'paid');

        if (!empty($validated['date_from'])) {
            $q->whereDate('paid_at', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $q->whereDate('paid_at', '<=', $validated['date_to']);
        }

        $payments = $q->orderByDesc('paid_at')->paginate(20)->withQueryString();

        $summary = [
            'total_collected' => Payment::where('status', 'paid')
                ->when(!empty($validated['date_from']), fn($q) => $q->whereDate('paid_at', '>=', $validated['date_from']))
                ->when(!empty($validated['date_to']), fn($q) => $q->whereDate('paid_at', '<=', $validated['date_to']))
                ->select(DB::raw('COALESCE(SUM(amount), 0) as total'))
                ->value('total'),
            'total_transactions' => Payment::where('status', 'paid')
                ->when(!empty($validated['date_from']), fn($q) => $q->whereDate('paid_at', '>=', $validated['date_from']))
                ->when(!empty($validated['date_to']), fn($q) => $q->whereDate('paid_at', '<=', $validated['date_to']))
                ->count(),
        ];

        return view('admin.reports.financial', compact('payments', 'summary'));
    }

    public function blotters(Request $request)
    {
        $validated = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        $q = Blotter::query()
            ->with(['complainantResident', 'respondentResident', 'case'])
            ->orderByDesc('incident_date');

        if (!empty($validated['date_from'])) {
            $q->whereDate('incident_date', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $q->whereDate('incident_date', '<=', $validated['date_to']);
        }

        $blotters = $q->paginate(20)->withQueryString();

        $summary = [
            'total_blotters' => Blotter::count(),
            'with_case' => Blotter::whereNotNull('case_id')->count(),
            'no_case' => Blotter::whereNull('case_id')->count(),
            'ongoing_cases' => CaseFile::where('status', 'ongoing')->count(),
            'settled_cases' => CaseFile::where('status', 'settled')->count(),
        ];

        return view('admin.reports.blotters', compact('blotters', 'summary'));
    }

    public function documents(Request $request)
    {
        $validated = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'status' => ['nullable', 'in:pending,ready,released'],
        ]);

        $q = DocumentRequest::query()
            ->with(['resident', 'documentType'])
            ->orderByDesc('created_at');

        if (!empty($validated['date_from'])) {
            $q->whereDate('created_at', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $q->whereDate('created_at', '<=', $validated['date_to']);
        }

        if (!empty($validated['status'])) {
            $q->where('status', $validated['status']);
        }

        $documents = $q->paginate(20)->withQueryString();

        $summary = [
            'total_requests' => DocumentRequest::count(),
            'pending' => DocumentRequest::where('status', 'pending')->count(),
            'ready' => DocumentRequest::where('status', 'ready')->count(),
            'released' => DocumentRequest::where('status', 'released')->count(),
        ];

        return view('admin.reports.documents', compact('documents', 'summary'));
    }
}