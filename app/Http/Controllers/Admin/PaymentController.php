<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\DocumentRequest;
use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'status' => ['nullable', 'in:pending,success,cancelled,failed'],
            'search' => ['nullable', 'string', 'max:255'],
            'page' => ['nullable', 'integer', 'min:1'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $limit = (int)($validated['limit'] ?? 10);

        $q = Payment::query()
            ->with(['documentRequest', 'resident', 'collector'])
            ->orderByDesc('id');

        if (!empty($validated['date_from'])) {
            $q->whereDate('created_at', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $q->whereDate('created_at', '<=', $validated['date_to']);
        }

        if (!empty($validated['status'])) {
            $q->where('status', $validated['status']);
        }

        if (!empty($validated['search'])) {
            $s = trim($validated['search']);
            $q->where(function ($w) use ($s) {
                $w->where('description', 'like', "%{$s}%")
                  ->orWhere('or_number', 'like', "%{$s}%")
                  ->orWhereHas('resident', function ($qr) use ($s) {
                      $qr->where('first_name', 'like', "%{$s}%")
                         ->orWhere('last_name', 'like', "%{$s}%");
                  });
            });
        }

        $payments = $q->paginate($limit)->withQueryString();

        $stats = [
            'total_collected' => Payment::where('status', 'success')
                ->select(DB::raw('COALESCE(SUM(amount), 0) as total'))
                ->value('total'),
            'success_count' => Payment::where('status', 'success')->count(),
            'pending_count' => Payment::where('status', 'pending')->count(),
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    public function create()
    {
        $residents = Resident::query()
            ->select('id', 'first_name', 'middle_name', 'last_name')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $documentRequests = DocumentRequest::query()
            ->whereNull('paid_at')
            ->with(['resident', 'documentType'])
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        return view('admin.payments.create', compact('residents', 'documentRequests'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'resident_id' => ['nullable', 'exists:residents,id'],
            'document_request_id' => ['nullable', 'exists:document_requests,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['required', 'string', 'max:255'],
            'payment_type' => ['required', 'in:document_fee,clearance,certification,other'],
            'or_number' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['status'] = 'pending';
        $validated['collected_by'] = auth()->id();

        $payment = Payment::create($validated);

        return redirect()
            ->route('admin.payments.show', $payment)
            ->with('success', 'Payment created successfully.');
    }

    public function show(Payment $payment)
    {
        $payment->load(['documentRequest', 'resident', 'collector']);

        return view('admin.payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $payment->load(['documentRequest', 'resident']);

        $residents = Resident::query()
            ->select('id', 'first_name', 'middle_name', 'last_name')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('admin.payments.edit', compact('payment', 'residents'));
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'resident_id' => ['nullable', 'exists:residents,id'],
            'document_request_id' => ['nullable', 'exists:document_requests,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['required', 'string', 'max:255'],
            'payment_type' => ['required', 'in:document_fee,clearance,certification,other'],
            'or_number' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $payment->update($validated);

        return redirect()
            ->route('admin.payments.show', $payment)
            ->with('success', 'Payment updated successfully.');
    }

    public function markAsPaid(Payment $payment)
    {
        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return redirect()
            ->route('admin.payments.show', $payment)
            ->with('success', 'Payment marked as paid.');
    }

    public function cancel(Payment $payment)
    {
        $payment->update(['status' => 'cancelled']);

        return redirect()
            ->route('admin.payments.show', $payment)
            ->with('success', 'Payment cancelled.');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return redirect()
            ->route('admin.payments.index')
            ->with('success', 'Payment deleted successfully.');
    }
}