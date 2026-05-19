<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseFile;
use App\Models\CaseHearing;
use App\Mail\HearingScheduled;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CaseController extends Controller
{
    public function index()
    {
        $cases = CaseFile::with('blotter')
            ->notArchived()
            ->orderByDesc('opened_at')
            ->paginate(15);

        return view('admin.cases.index', compact('cases'));
    }

    public function show(CaseFile $case)
    {
        $case->load(['blotter', 'hearings']);
        return view('admin.cases.show', compact('case'));
    }

    public function storeHearing(CaseFile $case, Request $request)
    {
        $data = $request->validate([
            'scheduled_at' => ['required', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'send_notification' => ['nullable', 'boolean'],
        ]);
        
        $sendNotification = $data['send_notification'] ?? true;
        
        if (in_array(strtolower($case->status), ['settled','dismissed','referred','archived'])) {
    return back()->with('error', 'This case is already closed and cannot be updated.');
}

        DB::transaction(function () use ($case, $data, $sendNotification) {
            $hearing = CaseHearing::create([
                'case_id' => $case->id,
                'scheduled_at' => $data['scheduled_at'],
                'location' => $data['location'] ?? 'Barangay Hall',
                'status' => 'scheduled',
                'notes' => $data['notes'] ?? null,
            ]);

            // if you schedule a hearing, mark case scheduled
            if (in_array($case->status, ['open', 'scheduled']) === false) return;
            $case->update(['status' => 'scheduled']);
            
            // Send email notifications
            if ($sendNotification) {
                $this->sendHearingNotifications($hearing);
            }
        });

        return back()->with('success', 'Hearing scheduled.');
    }
    
    /**
     * Send hearing notification emails to complainant and respondent.
     */
    protected function sendHearingNotifications(CaseHearing $hearing): void
    {
        $case = $hearing->case;
        $blotter = $case->blotter;
        
        if (!$blotter) return;
        
        // Send to complainant
        if ($blotter->complainant_email) {
            $complainantName = $blotter->complainantResident 
                ? $blotter->complainantResident->full_name 
                : $blotter->complainant_name;
            
            try {
                Mail::to($blotter->complainant_email)->send(
                    new HearingScheduled($hearing, $complainantName, $blotter->complainant_email, 'complainant')
                );
            } catch (\Exception $e) {
                // Log error but don't fail the request
                \Log::error('Failed to send hearing notification to complainant: ' . $e->getMessage());
            }
        }
        
        // Send to respondent
        if ($blotter->respondent_email) {
            $respondentName = $blotter->respondentResident 
                ? $blotter->respondentResident->full_name 
                : $blotter->respondent_name;
            
            try {
                Mail::to($blotter->respondent_email)->send(
                    new HearingScheduled($hearing, $respondentName, $blotter->respondent_email, 'respondent')
                );
            } catch (\Exception $e) {
                \Log::error('Failed to send hearing notification to respondent: ' . $e->getMessage());
            }
        }
    }

    public function close(CaseFile $case, Request $request)
    {
        $data = $request->validate([
            'status' => ['required', 'in:settled,referred,dismissed,archived'],
            'resolution_summary' => ['required', 'string'],
        ]);

        $case->update([
            'status' => $data['status'],
            'resolution_summary' => $data['resolution_summary'],
            'closed_at' => now(),
        ]);

        return back()->with('success', 'Case closed.');
    }
}
