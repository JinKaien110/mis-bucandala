<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blotter;
use App\Models\CaseFile;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\Household;
use App\Models\Resident;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AuditLog;

class AnalyticsController extends Controller
{
    protected $analyticsService;


    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function index(Request $request)
    {
        $dateFrom = $request->date_from ?? Carbon::now()->startOfYear()->toDateString();
        $dateTo = $request->date_to ?? Carbon::now()->toDateString();

        $filters = $request->all();
        $documentTypes = DocumentType::orderBy('name')->get();


        // Normalize filters so Incident Type and Case Type behave the same.
        // If the UI sends `case_type`, use it as `incident_type` for both blotters & cases.
        if (!empty($filters['case_type']) && empty($filters['incident_type'])) {
            $filters['incident_type'] = $filters['case_type'];
        }


        // Check granular permissions for each section
        $user = Auth::user();
        $permissions = $this->getAnalyticsPermissions($user);

        // Payment analytics for Payments tab should come directly from the payments table
        $paymentAnalyticsPayload = $this->analyticsService->getPaymentAnalytics($dateFrom, $dateTo);
        $overview = $this->getOverviewMetrics();
        // Ensure Payments tab always has a stable payload
        $overview['paymentAnalytics'] = $paymentAnalyticsPayload['paymentAnalytics'] ?? [];





        // Ensure overview totals respect document filters for the Documents tab stat card.
        // Currently the UI filters document requests via `document_type_id`.
        // If filtering by document type, sync the Executive stat card total with the filtered Documents dataset.
        if (!empty($filters['document_type_id'] ?? null)) {
            $baseQuery = DocumentRequest::whereBetween('created_at', [$dateFrom, $dateTo]);
            $baseQuery->when($filters['document_type_id'] ?? null, function ($q, $v) {
                $q->where('document_type_id', $v);
            });
            $overview['totalDocumentRequests'] = $baseQuery->count();
        }

        return view('admin.analytics.index', array_merge(
            $overview,
            $this->getResidentAnalytics($dateFrom, $dateTo, $filters),
            $this->getHouseholdAnalytics($filters),
            $this->getDocumentRequestAnalytics($dateFrom, $dateTo, $filters),
            $this->getBlotterAnalytics($dateFrom, $dateTo, $filters),
            $this->getCaseAnalytics($dateFrom, $dateTo, $filters),
            $this->getOfficialAnalytics($dateFrom, $dateTo),
            $this->getAnnouncementEventAnalytics($dateFrom, $dateTo),
            $this->getUserAdminAuditAnalytics($dateFrom, $dateTo),
            compact('dateFrom', 'dateTo', 'permissions', 'documentTypes')
        ));
    }


    // Export method now uses AnalyticsService and respects permissions
    public function export(Request $request)
    {
        $dateFrom = $request->date_from ?? Carbon::now()->startOfYear()->toDateString();
        $dateTo = $request->date_to ?? Carbon::now()->toDateString();
        $format = $request->format ?? 'csv';

        $user = Auth::user();
        $permissions = $this->getAnalyticsPermissions($user);

        $filters = $request->all();


        // Normalize filters so Incident Type and Case Type behave the same.
        if (!empty($filters['case_type']) && empty($filters['incident_type'])) {
            $filters['incident_type'] = $filters['case_type'];
        }

        $data = [];

        // Export only requested sections (from modal checkboxes)
        $requestedSections = (array) $request->input('export_sections', []);

        // Map checkbox values to export payload keys + permission gate
        $sectionHandlers = [
            'executive' => ['perm' => 'viewExecutiveDashboard', 'key' => 'Overview', 'fn' => fn() => $this->getOverviewMetrics()],
            'residents' => ['perm' => 'viewResidentAnalytics', 'key' => 'Residents', 'fn' => fn() => $this->getResidentAnalytics($dateFrom, $dateTo, $filters)],
            'households' => ['perm' => 'viewHouseholdAnalytics', 'key' => 'Households', 'fn' => fn() => $this->getHouseholdAnalytics($filters)],
            'documents' => ['perm' => 'viewDocumentRequestAnalytics', 'key' => 'Documents', 'fn' => fn() => $this->getDocumentRequestAnalytics($dateFrom, $dateTo, $filters)],
            'payments' => ['perm' => 'viewPaymentAnalytics', 'key' => 'Payments', 'fn' => fn() => $this->analyticsService->getPaymentAnalytics()],
            'blotters' => ['perm' => 'viewBlotterAnalytics', 'key' => 'Blotters', 'fn' => fn() => $this->getBlotterAnalytics($dateFrom, $dateTo, $filters)],
            'cases' => ['perm' => 'viewCaseAnalytics', 'key' => 'Cases', 'fn' => fn() => $this->getCaseAnalytics($dateFrom, $dateTo, $filters)],
            'officials' => ['perm' => 'viewOfficialAnalytics', 'key' => 'Officials', 'fn' => fn() => $this->getOfficialAnalytics($dateFrom, $dateTo)],
            'announcements-events' => ['perm' => 'viewAnnouncementEventAnalytics', 'key' => 'AnnouncementsEvents', 'fn' => fn() => $this->getAnnouncementEventAnalytics($dateFrom, $dateTo)],
            'users-audit' => ['perm' => 'viewAdminSystemAnalytics', 'key' => 'UsersAdminsAudit', 'fn' => fn() => $this->getUserAdminAuditAnalytics($dateFrom, $dateTo)],
        ];

        // If nothing specified, keep current behavior: export all permitted sections.
        // Otherwise, export exactly what the user selected.
        // Also: only accept known section keys from the UI to avoid stray/extra params.
        $sectionsToExport = empty($requestedSections)
            ? array_keys($sectionHandlers)
            : array_values(array_unique(array_values(array_intersect($requestedSections, array_keys($sectionHandlers)))));



        foreach ($sectionsToExport as $section) {
            if (!isset($sectionHandlers[$section])) {
                continue;
            }

            $perm = $sectionHandlers[$section]['perm'];
            if (!($permissions[$perm] ?? false)) {
                continue;
            }

            $key = $sectionHandlers[$section]['key'];
            $value = $sectionHandlers[$section]['fn']();

            if ($key === 'Overview' && $section === 'payments') {
                // Merge payments analytics into executive overview
                $data['Overview'] = $data['Overview'] ?? $this->getOverviewMetrics();
                $data['Overview'] = array_merge($data['Overview'], $value);
                continue;
            }

            $data[$key] = $value;

        }



        if ($format === 'csv') {
        return $this->analyticsService->exportAsCsv($data, $dateFrom, $dateTo);

        }

        // PDF variants
        if ($format === 'pdf' || $format === 'pdf_charts_table_lists') {
            $includeRecordTables = $format === 'pdf_charts_table_lists';

            // Include Overview only when Executive Dashboard is selected.
            // This prevents Executive Summary from appearing in PDF when user unchecks it.
            $selectedSections = empty($requestedSections)
                ? array_keys($sectionHandlers)
                : array_values(array_unique(array_values(array_intersect($requestedSections, array_keys($sectionHandlers)))));

            // NOTE: We only control the Executive Summary (Overview).
            // Residents/Demography must remain independent.
            if (in_array('executive', $selectedSections, true)) {
                if (!isset($data['Overview'])) {
                    $data['Overview'] = $this->getOverviewMetrics();
                } else {
                    $data['Overview'] = array_merge($data['Overview'], $this->analyticsService->getPaymentAnalytics());
                }
            } else {
                unset($data['Overview']);
            }

            // ------------------------------------------------------------
// Attach record tables for PDF (Charts + Table Lists)
// ------------------------------------------------------------
if ($includeRecordTables) {

    // Resident Records
    if (in_array('residents', $selectedSections, true)) {

        $data['ResidentsList'] = Resident::query()
            ->orderBy('last_name')
            ->get()
            ->toArray();
    }

        // Household table
    if (in_array('households', $selectedSections, true)) {

      $data['HouseholdsList'] = Household::with('members.resident')
    ->orderByDesc('id')
    ->get()
    ->map(function ($household) {

        $headMember = $household->members
            ->firstWhere('relationship', 'Head');

        $resident = $headMember?->resident;

        return [
            'id' => $household->id,
            'household_code' => $household->household_code,
            'address_line' => $household->address_line,
            'phase' => $household->phase,

            // computed
            'members_count' => $household->members->count(),

            'head_fullname' => $resident
                ? trim($resident->first_name . ' ' . $resident->last_name)
                : null,

            'head_contact' => $resident->contact_no ?? null,
        ];
    })
    ->toArray();
    }

    if (in_array('documents', $selectedSections, true)) {

    $data['DocumentsList'] = \App\Models\DocumentRequest::with([
        'resident',
        'documentType'
    ])
    ->orderByDesc('id')
    ->get()
    ->map(function ($document) {

        return [
            'control_no' => $document->control_no,

            'resident_name' => $document->resident
                ? trim(
                    $document->resident->last_name . ', ' .
                    $document->resident->first_name . ' ' .
                    ($document->resident->middle_name ?? '') . ' ' .
                    ($document->resident->suffix ?? '')
                )
                : '-',

            'document_name' => $document->documentType->name ?? '-',

            'fee_amount' => $document->fee_amount,

            'requested_at' => optional($document->created_at)
                ->format('M d, Y h:i A'),

            'purpose' => $document->purpose,
        ];

    })
    ->toArray();
    }

    $data['PaymentsList'] = \App\Models\Payment::with([
    'resident',
    'documentRequest.documentType',
])
->orderByDesc('id')
->get()
->map(function ($payment) {
    

    return [

        'control_no' => $payment->documentRequest->control_no ?? '-',

        'document_type' => $payment->documentRequest->documentType->name ?? '-',

        'resident_name' => $payment->documentRequest->resident
            ? trim(
                $payment->documentRequest->resident->last_name . ', ' .
                $payment->documentRequest->resident->first_name . ' ' .
                ($payment->documentRequest->resident->middle_name ?? '') . ' ' .
                ($payment->documentRequest->resident->suffix ?? '')
            )
            : '-',

        'amount' => $payment->amount,

        'status' => ucfirst($payment->status),

        'date' => optional($payment->paid_at ?? $payment->created_at)
            ->format('M d, Y h:i A'),

    ];

})
->toArray();

    if (in_array('blotters', $selectedSections, true)) {

        $data['BlottersList'] = \App\Models\Blotter::query()
    ->withExists('case')
    ->orderByDesc('id')
    ->get([
        'blotter_no',
        'complainant_name',
        'respondent_name',
        'incident_date',
        'incident_type',
        'incident_location',
        'status',
    ])
    ->map(function ($blotter) {

        return [
            'blotter_no'    => $blotter->blotter_no,
            'incident_date'     => $blotter->incident_date,
            'incident_type'     => $blotter->incident_type,
            'complainant_name'  => $blotter->complainant_name,
            'respondent_name'   => $blotter->respondent_name,
            'incident_location' => $blotter->incident_location,
            'status'            => $blotter->status,
           'has_case' => $blotter->case !== null,
           'case_no' => $blotter->case?->case_no,
        ];

    })
    ->toArray();

    }


    $data['CasesList'] = \App\Models\CaseFile::with('blotter')
    ->orderByDesc('id')
    ->get()
    ->map(function ($case) {

        return [

            'case_no' => $case->case_no,

            'blotter_no' => $case->blotter->blotter_no ?? '-',

            'complainant_name' => $case->blotter->complainant_name ?? '-',

            'respondent_name' => $case->blotter->respondent_name ?? '-',

            'status' => ucfirst($case->status),

            'opened_at' => optional($case->opened_at ?? $case->created_at)
                ->format('M d, Y h:i A'),

        ];

    })
    ->toArray();

    if (in_array('announcements-events', $selectedSections, true)) {

    $data['AnnouncementsList'] = \App\Models\Announcement::query()
        ->orderByDesc('id')
        ->get([
            'title',
            'content',
            'type',
            'publish_date',
        ])
        ->toArray();

    $data['EventsList'] = \App\Models\Event::query()
        ->orderByDesc('id')
        ->get([
            'title',
            'description',
            'start_datetime',
            'end_datetime',
            'location',
            'type',
            'created_by',
        ])
        ->toArray();
}

if (in_array('users-audit', $selectedSections, true)) {

    $data['AuditLogsList'] = AuditLog::with('user')
        ->latest('created_at')
        ->get()
        ->map(function ($log) {

            return [

                'datetime' => optional($log->created_at)->format('M d, Y h:i A'),

                'user' => $log->user?->name ?? 'System',

                'email' => $log->user?->email ?? '-',

                'module' => ucfirst($log->module),

                'action' => ucfirst($log->action),

                'record_id' => $log->record_id,

                'ip_address' => $log->ip_address,

                'old_data' => $log->old_data,

                'new_data' => $log->new_data,

            ];

        })
        ->toArray();

}
}


            return $this->analyticsService->exportAsPdf($data, $dateFrom, $dateTo, $selectedSections, $includeRecordTables);
        }

        return response()->json(['message' => 'JSON export not implemented yet'], 501);
    }

    /**
     * Get permission flags for the current user.
     */
    private function getAnalyticsPermissions(User $user): array
    {
        $role = $user?->getAnalyticsRoleAttribute();

        // Granular analytics permissions (RBAC)
        // Required by: role.users: admin, staff, and admins.position: Barangay Captain, Barangay Secretary
        return [
            'viewExecutiveDashboard' => in_array($role, ['captain', 'secretary'], true),
            // (no-op)
            'viewResidentAnalytics' => in_array($role, ['captain', 'secretary', 'clerk'], true),
            'viewHouseholdAnalytics' => in_array($role, ['captain', 'secretary'], true),
            'viewDocumentRequestAnalytics' => in_array($role, ['captain', 'secretary', 'clerk'], true),
            'viewPaymentAnalytics' => in_array($role, ['captain', 'secretary', 'treasurer'], true),
            'viewBlotterAnalytics' => in_array($role, ['captain', 'secretary', 'lupon'], true),
            'viewCaseAnalytics' => in_array($role, ['captain', 'secretary', 'lupon'], true),
            'viewOfficialAnalytics' => in_array($role, ['captain', 'secretary'], true),
            'viewAnnouncementEventAnalytics' => in_array($role, ['captain', 'secretary', 'clerk'], true),
            'viewAdminSystemAnalytics' => in_array($role, ['captain', 'secretary'], true),
        ];
    }

    // Moved CSV export logic to AnalyticsService
    /*
    private function exportAsCsv(array $data, string $dateFrom, string $dateTo)
    {
        $filename = 'analytics-export-'.now()->format('Y-m-d-H-i-s').'.csv';
        $callback = function () use ($data, $dateFrom, $dateTo) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, ['Analytics Dashboard Export - Barangay Bucandala 1']);
            fputcsv($file, ['Period:', $dateFrom.' to '.$dateTo]);
            fputcsv($file, ['Generated:', Carbon::now()->format('Y-m-d H:i:s')]);
            fputcsv($file, ['']);

            foreach ($data as $section => $sectionData) {
                fputcsv($file, ["=== {$section} ==="]);

                if (is_array($sectionData)) {
                    $this->writeCsvArray($file, $sectionData);
                }

                fputcsv($file, ['']);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }*/

    // Moved PDF export logic to AnalyticsService
    /*
    private function exportAsPdf(array $data, string $dateFrom, string $dateTo)
    {
        $overview = $data['Overview'];
        $residents = $data['Residents'];
        $households = $data['Households'];
        $documents = $data['Documents'];
        $blotters = $data['Blotters'];
        $cases = $data['Cases'];

        $charts = [
            'genderPie' => $this->generatePieChartSvg($residents['genderStats'] ?? [], ['Male' => '#3b82f6', 'Female' => '#ec4899']),
            'ageBar' => $this->generateBarChartSvg($residents['ageGroups'] ?? [], 'Age Distribution'),
            'occupationBar' => $this->generateBarChartSvg($residents['occupationStats'] ?? [], 'Top Occupations', 200),
            'docTypePie' => $this->generatePieChartSvg($documents['topTypes'] ?? [], ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6']),
            'incidentPie' => $this->generatePieChartSvg(array_slice($blotters['incidentTypeStats'] ?? [], 0, 5), ['#ef4444', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6']),
        ];

        $html = view('admin.analytics.pdf', compact(
            'dateFrom', 'dateTo', 'overview', 'residents', 'households',
            'documents', 'blotters', 'cases', 'charts'
        ))->render();

        $dompdf = app('dompdf');
        $dompdf->loadHtml($html);
        $dompdf->render();

        $filename = 'analytics-report-'.now()->format('Y-m-d-H-i-s').'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }*/

    // Moved SVG chart generation to AnalyticsService
    /*
    private function generatePieChartSvg(array $data, array $colors = [])
    {
        if (empty($data)) {
            return '';
        }

        $total = array_sum($data);
        if ($total == 0) {
            return '';
        }

        $svg = '<svg width="200" height="200" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">';
        $svg .= '<circle cx="100" cy="100" r="80" fill="white"/>';

        $angles = [];
        $startAngle = 0;
        $colorKeys = array_keys($colors);
        $i = 0;

        foreach ($data as $label => $value) {
            $percent = $value / $total;
            $endAngle = $startAngle + ($percent * 360);
            $angles[] = ['start' => $startAngle, 'end' => $endAngle, 'label' => $label, 'value' => $value];
            $startAngle = $endAngle;
            $i++;
        }

        $prevAngle = 0;
        foreach ($angles as $index => $angle) {
            $color = isset($colorKeys[$index]) ? $colors[$colorKeys[$index]] : sprintf('#%06x', rand(0, 16777215));
            $startRad = deg2rad($angle['start'] - 90);
            $endRad = deg2rad($angle['end'] - 90);

            $x1 = 100 + 80 * cos($startRad);
            $y1 = 100 + 80 * sin($startRad);
            $x2 = 100 + 80 * cos($endRad);
            $y2 = 100 + 80 * sin($endRad);

            $largeArc = ($angle['end'] - $angle['start']) > 180 ? 1 : 0;

            $path = "M 100 100 L $x1 $y1 A 80 80 0 $largeArc 1 $x2 $y2 Z";
            $svg .= "<path d=\"$path\" fill=\"$color\" opacity=\"0.85\"/>";
        }

        $svg .= '<circle cx="100" cy="100" r="40" fill="white"/>';
        $svg .= '</svg>';

        return $svg;
    }

    private function generateBarChartSvg(array $data, string $title = '', int $height = 150)
    {
        if (empty($data)) {
            return '';
        }

        $maxValue = max($data);
        if ($maxValue == 0) {
            $maxValue = 1;
        }

        $barWidth = floor(180 / count($data));
        $svg = '<svg width="200" height="'.($height + 30).'" viewBox="0 0 200 '.($height + 30).'" xmlns="http://www.w3.org/2000/svg">';
        $svg .= '<rect width="200" height="'.($height + 30).'" fill="white"/>';

        $colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16'];
        $i = 0;

        foreach ($data as $label => $value) {
            $barHeight = ($value / $maxValue) * $height;
            $x = 10 + ($i * $barWidth);
            $y = $height - $barHeight;
            $color = $colors[$i % count($colors)];

            $svg .= "<rect x=\"$x\" y=\"$y\" width=\"".($barWidth - 4)."\" height=\"$barHeight\" fill=\"$color\" rx=\"2\"/>";
            $svg .= '<text x="'.($x + ($barWidth - 4) / 2).'" y="'.($height + 15).'" font-size="8" text-anchor="end" transform="rotate(45 '.($x + ($barWidth - 4) / 2).' '.($height + 15).')">'.substr($label, 0, 8).'</text>';
            $i++;
        }

        $svg .= '</svg>';

        return $svg;
    }

    private function writeCsvArray($file, array $data, $indent = 0)
    {
        foreach ($data as $key => $value) {
            $prefix = str_repeat('  ', $indent);

            if (is_array($value)) {
                fputcsv($file, [$prefix.$key.':']);
                $this->writeCsvArray($file, $value, $indent + 1);
            } elseif (is_numeric($value)) {
                fputcsv($file, [$prefix.$key, number_format($value)]);
            } else {
                fputcsv($file, [$prefix.$key, $value]);
            }
        }
    }*/

        

    private function getOverviewMetrics()
    {
        $now = Carbon::now();
        $lastMonth = $now->copy()->subMonth();

        $overview = [
            'totalResidents' => $this->analyticsService->getTotalResidents(),
            'totalHouseholds' => $this->analyticsService->getTotalHouseholds(),
            'totalDocumentRequests' => $this->analyticsService->getTotalDocumentRequests(),
            'totalBlotters' => $this->analyticsService->getTotalBlotters(),
            'totalCases' => $this->analyticsService->getTotalCases(),
            'totalUsers' => $this->analyticsService->getTotalUsers(),
            'residentGrowth' => $this->analyticsService->getGrowthPercentage(Resident::class, 'last_month', $lastMonth),
            'documentRequestGrowth' => $this->analyticsService->getGrowthPercentage(DocumentRequest::class, 'last_month', $lastMonth),
            'blotterGrowth' => $this->analyticsService->getGrowthPercentage(Blotter::class, 'last_month', $lastMonth),
            'recentActivities' => $this->getRecentActivities(),
        ];

        return array_merge($overview, $this->analyticsService->getPaymentAnalytics());
    }

    //     $currentCount = $model::when($period === 'last_month', function ($q) use ($compareDate) {
    //         $q->whereMonth('created_at', '=', $compareDate->month)
    //             ->whereYear('created_at', '=', $compareDate->year);
    //     })->count();

    //     $previousPeriod = $compareDate->copy()->subMonth();
    //     $previousCount = $model::whereMonth('created_at', '=', $previousPeriod->month)
    //         ->whereYear('created_at', '=', $previousPeriod->year)
    //         ->count();

    //     if ($previousCount === 0) {
    //         return $currentCount > 0 ? 100 : 0;
    //     }
    //     return round((($currentCount - $previousCount) / $previousCount) * 100);
    // }

    private function getRecentActivities()
    {
        $recentResidents = Resident::latest()->take(5)->get()->map(function ($r) {
            return [
                'type' => 'resident',
                'title' => 'New resident registered',
                'description' => $r->first_name.' '.$r->last_name,
                'created_at' => $r->created_at,
            ];
        });

        $recentDocuments = DocumentRequest::with('resident')->latest()->take(5)->get()->map(function ($d) {
            return [
                'type' => 'document',
                'title' => 'Document requested',
                'description' => $d->documentType->name ?? 'Unknown',
                'created_at' => $d->created_at,
            ];
        });

        $recentBlotters = Blotter::latest()->take(5)->get()->map(function ($b) {
            return [
                'type' => 'blotter',
                'title' => 'Blotter filed',
                'description' => $b->incident_type,
                'created_at' => $b->created_at,
            ];
        });

        return $recentResidents->concat($recentDocuments)->concat($recentBlotters)
            ->sortByDesc('created_at')
            ->take(10)
            ->values();
    }

    private function getResidentAnalytics(string $dateFrom, string $dateTo, array $filters = [])
    {
        $baseQuery = Resident::whereBetween('created_at', [$dateFrom, $dateTo]);
        
        $baseQuery = $this->applyResidentFilters($baseQuery, $filters);

        // Gender distribution
        $genderStats = (clone $baseQuery)->select('sex', DB::raw('COUNT(*) as count'))
            ->groupBy('sex')
            ->pluck('count', 'sex')
            ->toArray();

        // Age groups
        $ageGroups = [
            '0-17' => (clone $baseQuery)->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 0 AND 17')->count(),
            '18-30' => (clone $baseQuery)->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 18 AND 30')->count(),
            '31-50' => (clone $baseQuery)->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 31 AND 50')->count(),
            '51-65' => (clone $baseQuery)->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 51 AND 65')->count(),
            '65+' => (clone $baseQuery)->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= 65')->count(),
        ];

        // Civil status
        $civilStatusStats = (clone $baseQuery)->select('civil_status', DB::raw('COUNT(*) as count'))
            ->groupBy('civil_status')
            ->pluck('count', 'civil_status')
            ->toArray();

        // Occupation (top 10)
        $occupationStats = (clone $baseQuery)->select('occupation', DB::raw('COUNT(*) as count'))
            ->groupBy('occupation')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'occupation')
            ->toArray();

        // Status distribution (active/inactive)
        $statusStats = (clone $baseQuery)->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')->pluck('count', 'status')->toArray();

        // Inactive/Archived count (archived_at is not null)
        $inactiveCount = (clone $baseQuery)->whereNotNull('archived_at')->count();

        // New Resident Analytics
        $totalResidents = (clone $baseQuery)->count();
        // Monthly trends
        $residentMonthlyTrends = (clone $baseQuery)->select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Population by phase
        $phasePopulation = (clone $baseQuery)->select('phase', DB::raw('COUNT(*) as count'))
            ->groupBy('phase')->pluck('count', 'phase')->toArray();

        // PWD, Solo Parent, Indigent, 4Ps Beneficiaries
        $pwdResidents = (clone $baseQuery)->where('pwd', true)->count();
        $soloParentResidents = (clone $baseQuery)->where('solo_parent', true)->count();
        $indigentResidents = (clone $baseQuery)->where('indigent', true)->count();
        $fourPsBeneficiaries = (clone $baseQuery)->where('four_ps_beneficiary', true)->count();

        // Age segments
        $minorPopulation = (clone $baseQuery)
            ->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) < 18')
            ->count();

        // Adult = 18..64 (Senior = 65+)
        $adultPopulation = (clone $baseQuery)
            ->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= 18')
            ->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) < 65')
            ->count();

        $seniorCitizens = (clone $baseQuery)
            ->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= 65')
            ->count();

        // Average age
        $averageAge = (clone $baseQuery)
            ->whereNotNull('birth_date')
            ->selectRaw('AVG(TIMESTAMPDIFF(YEAR, birth_date, CURDATE())) as avg_age')
            ->value('avg_age');

        $averageAge = $averageAge !== null ? round((float) $averageAge, 1) : 0;


        return compact(
            'totalResidents', 'genderStats', 'ageGroups', 'civilStatusStats', 'occupationStats',
            'statusStats', 'inactiveCount', 'residentMonthlyTrends', 'phasePopulation',
            'pwdResidents', 'soloParentResidents', 'indigentResidents', 'fourPsBeneficiaries',
            'minorPopulation', 'adultPopulation', 'seniorCitizens', 'averageAge'
        );
    }

    private function applyResidentFilters($query, array $filters)
    {
        return $query->when($filters['sex'] ?? null, fn($q, $v) => $q->where('sex', $v))
            ->when($filters['account_no'] ?? null, fn($q, $v) => $q->where('account_no', 'like', "%{$v}%"))
            ->when($filters['resident_name'] ?? null, function($q, $v) {
                $q->where(fn($sub) => $sub->where('first_name', 'like', "%{$v}%")->orWhere('last_name', 'like', "%{$v}%"));
            })
            ->when($filters['address_line'] ?? null, fn($q, $v) => $q->where('address_line', 'like', "%{$v}%"))
            ->when($filters['civil_status'] ?? null, fn($q, $v) => $q->where('civil_status', $v))
            ->when($filters['phase'] ?? null, function ($q, $v) {
                if ($v === 'no_phase') {
                    return $q->whereNull('phase');
                }
                return $q->where('phase', 'like', "%{$v}%");
            })
            ->when($filters['occupation'] ?? null, fn($q, $v) => $q->where('occupation', 'like', "%{$v}%"))
            ->when($filters['educational_attainment'] ?? null, fn($q, $v) => $q->where('educational_attainment', $v))
            ->when($filters['verification_type'] ?? null, fn($q, $v) => $q->where('verification_type', $v))
            ->when($filters['min_age'] ?? null, fn($q, $v) => $q->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= ?', [$v]))
            ->when($filters['max_age'] ?? null, fn($q, $v) => $q->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) <= ?', [$v]))
            ->when($filters['is_pwd'] ?? null, fn($q) => $q->where('pwd', true))
            ->when($filters['is_senior'] ?? null, fn($q) => $q->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= 65'))
            ->when($filters['is_indigent'] ?? null, fn($q) => $q->where('indigent', true))
            ->when($filters['is_4ps'] ?? null, fn($q) => $q->where('four_ps_beneficiary', true))
            ->when($filters['is_solo_parent'] ?? null, fn($q) => $q->where('solo_parent', true));
    }

    private function getHouseholdAnalytics(array $filters = [])
    {
        $dateFrom = request()->date_from ?? Carbon::now()->startOfYear()->toDateString();
        $dateTo = request()->date_to ?? Carbon::now()->toDateString();

        $baseQuery = Household::whereBetween('created_at', [$dateFrom, $dateTo]);
        $baseQuery = $this->applyHouseholdFilters($baseQuery, $filters);

        // Total households (Filtered)
        $totalHouseholds = (clone $baseQuery)->count();

        // Average members per household - manually count from household_members table
        $totalMembers = DB::table('household_members')->count();
        $avgMembers = $totalHouseholds > 0 ? round($totalMembers / $totalHouseholds, 1) : 0;

        // Households by street
        $streetStats = (clone $baseQuery)->select('address_line', DB::raw('COUNT(*) as count'))
            ->groupBy('address_line') 
            ->orderByDesc('count')
            ->pluck('count', 'address_line')
            ->toArray();

        // Households by phase
        // Households by Phase
        // Expected values: 'A', 'B', 'C', 'No Phase' (or null/empty)
        $phaseStatsRaw = (clone $baseQuery)
            ->select('phase', DB::raw('COUNT(*) as count'))
            ->groupBy('phase')
            ->pluck('count', 'phase')
            ->toArray();

        // Normalize keys so the chart always shows A/B/C and No Phase
        $phaseStats = [
            'A' => (int) ($phaseStatsRaw['A'] ?? 0),
            'B' => (int) ($phaseStatsRaw['B'] ?? 0),
            'C' => (int) ($phaseStatsRaw['C'] ?? 0),
            'No Phase' => (int) (
                ($phaseStatsRaw[null] ?? 0) +
                ($phaseStatsRaw[''] ?? 0) +
                ($phaseStatsRaw['No Phase'] ?? 0)
            ),
        ];

        // If DB uses other representations (like 'NULL' string), aggregate them too
        foreach ($phaseStatsRaw as $k => $v) {
            if ($k === 'A' || $k === 'B' || $k === 'C' || $k === 'No Phase' || $k === null || $k === '') {
                continue;
            }
            $phaseStats['No Phase'] += (int) $v;
        }

        
        // Households by household type
        $householdTypeStats = (clone $baseQuery)->select('household_type', DB::raw('COUNT(*) as count'))
            ->groupBy('household_type')
            ->pluck('count', 'household_type')
            ->toArray();

        // Households by homeownership type
        $homeownershipStats = (clone $baseQuery)->select('homeownership_type', DB::raw('COUNT(*) as count'))
            ->groupBy('homeownership_type')
            ->pluck('count', 'homeownership_type')
            ->toArray();

        // Socio-economic: Income range distribution (use the new string column)
        $incomeRangeStats = (clone $baseQuery)->select('monthly_income_range', DB::raw('COUNT(*) as count'))
            ->groupBy('monthly_income_range')
            ->pluck('count', 'monthly_income_range')
            ->toArray();

        // 4Ps beneficiaries (computed via household flags / member flags)
        $fourPsCount = (clone $baseQuery)->where('is_4ps_beneficiary', true)->count();

        // Indigent / Senior / PWD should be computed from residents table (member attributes)
        // using JOIN/relationship logic:
        //   residents.is_indigent, residents.is_senior, residents.is_pwd
        // If your implementation uses different columns, adjust accordingly.
        $indigentCount = (clone $baseQuery)->whereHas('residents', function ($q) {
            $q->where('indigent', true);
        })->count();

        $pregnantCount = (clone $baseQuery)->whereHas('members', function ($q) {
            $q->where('has_pregnant_member', true);
        })->count();

        // Senior citizens in PH is considered at age >= 60
        $seniorCount = (clone $baseQuery)->whereHas('residents', function ($q) {
            $q->whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= 60');
        })->count();

        $pwdCount = (clone $baseQuery)->whereHas('residents', function ($q) {
            $q->where('pwd', true);
        })->count();

        $chronicCount = (clone $baseQuery)->whereHas('members', function ($q) {
            $q->where('has_chronic_illness', true);
        })->count();

        // Housing types
        $houseTypeStats = (clone $baseQuery)->select('house_type', DB::raw('COUNT(*) as count'))
            ->groupBy('house_type')
            ->pluck('count', 'house_type')
            ->toArray();

        // Utilities access
        $electricityAccess = (clone $baseQuery)->where('has_electricity', true)->count();
        $toiletAccess = (clone $baseQuery)->where('has_toilet', true)->count();
        $bathroomAccess = (clone $baseQuery)->where('has_bathroom', true)->count();
        $kitchenAccess = (clone $baseQuery)->where('has_kitchen', true)->count();
        $garageAccess = (clone $baseQuery)->where('has_garage', true)->count();

        // Disaster risk level
        $disasterRiskStats = (clone $baseQuery)->select('disaster_risk_level', DB::raw('COUNT(*) as count'))
            ->groupBy('disaster_risk_level')
            ->pluck('count', 'disaster_risk_level')
            ->toArray();

        // Monthly trends
        $householdMonthlyTrends = (clone $baseQuery)->select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        return compact(
            'totalHouseholds',
            'avgMembers',
            'streetStats',
            'phaseStats',
            'householdTypeStats',
            'homeownershipStats',
            'incomeRangeStats',
            'fourPsCount',

            'indigentCount',
            'pregnantCount',
            'seniorCount',
            'pwdCount',
            'chronicCount',
            'houseTypeStats',
            'electricityAccess',
            'toiletAccess',
            'bathroomAccess',
            'kitchenAccess',
            'garageAccess',
            'disasterRiskStats',
            'householdMonthlyTrends'
        );
    }

    private function applyHouseholdFilters($query, array $filters)
    {
        return $query
            // Use a dedicated household phase filter so it doesn't conflict with resident phase
            // Supports "No Phase" selection via sentinel value `no_phase` => phase IS NULL
            ->when($filters['household_phase'] ?? null, function ($q, $v) {
                if ($v === 'no_phase') {
                    return $q->whereNull('phase');
                }
                return $q->where('phase', 'like', "%{$v}%");
            })

            ->when($filters['income_range'] ?? null, fn($q, $v) => $q->where('monthly_income_range', $v))
            ->when($filters['homeownership_type'] ?? null, fn($q, $v) => $q->where('homeownership_type', $v))
            ->when($filters['has_electricity'] ?? null, fn($q) => $q->where('has_electricity', true))
            ->when($filters['has_water'] ?? null, fn($q) => $q->where('has_water', true))
            ->when($filters['has_toilet'] ?? null, fn($q) => $q->where('has_toilet', true))
            ->when($filters['has_garage'] ?? null, fn($q) => $q->where('has_garage', true))

            // Flags coming from residents table through household_members
            // so filtering is: household -> household_members -> residents
            ->when($filters['is_solo_parent'] ?? null, fn($q) => $q->whereHas('members.resident', function ($r) {
                $r->where('solo_parent', true);
            }))
            ->when($filters['is_pwd'] ?? null, fn($q) => $q->whereHas('members.resident', function ($r) {
                $r->where('pwd', true);
            }))
            ->when($filters['is_indigent'] ?? null, fn($q) => $q->whereHas('members.resident', function ($r) {
                $r->where('indigent', true);
            }))
            ->when($filters['is_4ps'] ?? null, fn($q) => $q->whereHas('members.resident', function ($r) {
                $r->where('four_ps_beneficiary', true);
            }))
            ->when($filters['barangay_program_participation'] ?? null, function ($q) use ($filters) {
                // NOTE: optional; only filters if you already handle program participation on household model
                // Not implemented server-side because program participation is stored as array/string on household.
            });
    }

    private function getDocumentRequestAnalytics(string $dateFrom, string $dateTo, array $filters = [])
    {
        $baseQuery = DocumentRequest::whereBetween('created_at', [$dateFrom, $dateTo]);

        $baseQuery->when($filters['document_type_id'] ?? null, function ($q, $v) {
            $q->where('document_type_id', $v);
        });


        // By status
        $docStatusStats = (clone $baseQuery)->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // By document type
        $typeStats = (clone $baseQuery)->select('document_type_id', DB::raw('COUNT(*) as count'))
            ->groupBy('document_type_id')
            ->with('documentType')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->documentType->name ?? 'Unknown' => $item->count];
            })
            ->toArray();

        // Monthly trends
        $docMonthlyTrends = (clone $baseQuery)->select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Processing time (average days from request to now/resolved)
        $avgProcessingDays = (clone $baseQuery)->selectRaw('AVG(TIMESTAMPDIFF(DAY, created_at, NOW())) as avg_days')
            ->value('avg_days') ?? 0;

        // Total requests in the same filtered dataset (so PDF is correct even without Executive section)
        $totalDocumentRequests = (clone $baseQuery)->count();


        // Approval rate
        $totalProcessed = (clone $baseQuery)->whereIn('status', ['approved', 'released'])->count();
        $totalRequests = (clone $baseQuery)->count();
        $approvalRate = $totalRequests > 0 ? round(($totalProcessed / $totalRequests) * 100) : 0;

        // Top requested types (respect currently applied filters/date range)
        $topTypes = (clone $baseQuery)
            ->select('document_type_id', DB::raw('COUNT(*) as count'))
            ->groupBy('document_type_id')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        $topTypes = $topTypes->mapWithKeys(function ($row) {
            $name = $row->documentType->name ?? 'Unknown';
            return [$name => (int) $row->count];
        })->toArray();

        // Keep backwards/forward template compatibility
        // - pdf.blade.php expects: avgDays
        // - we also provide totalDocumentRequests so Documents paragraph never depends on $overview
        return [
            'docStatusStats' => $docStatusStats,
            'docMonthlyTrends' => $docMonthlyTrends,
            'avgProcessingDays' => $avgProcessingDays,
            'avgDays' => $avgProcessingDays,
            'totalDocumentRequests' => $totalDocumentRequests,
            'approvalRate' => $approvalRate,
            'topTypes' => $topTypes,
        ];


    }

    private function getBlotterAnalytics(string $dateFrom, string $dateTo, array $filters = [])
    {

        $incidentTypeQuery = Blotter::whereBetween('created_at', [$dateFrom, $dateTo]);

        // Apply Peace & Order filter (Incident Type)
        if (!empty($filters['incident_type'])) {
            $incidentTypeQuery->where('incident_type', $filters['incident_type']);
        }

        // By incident type
        $incidentTypeStats = (clone $incidentTypeQuery)
            ->select('incident_type', DB::raw('COUNT(*) as count'))
            ->groupBy('incident_type')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'incident_type')
            ->toArray();


        // Monthly trends
        $blotterMonthlyTrendsQuery = Blotter::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereYear('created_at', '=', Carbon::now()->year);

        if (!empty($filters['incident_type'])) {
            $blotterMonthlyTrendsQuery->where('incident_type', $filters['incident_type']);
        }

        $blotterMonthlyTrends = $blotterMonthlyTrendsQuery
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Resolution rate
        // Business rule: a blotter with NO corresponding case is considered resolved.
        // Therefore, resolved blotters are:
        //   (status = 'resolved') OR (has NO case)
        $totalQuery = Blotter::whereBetween('created_at', [$dateFrom, $dateTo]);

        // Status in blotters table is always `filed` (so it's irrelevant here).
        // Resolution rate should be computed strictly by whether the blotter has a related case.
        // Requirement: if no case is found, count the blotter as RESOLVED.
        // With blotter statuses always "filed", we base resolution purely on relationship existence.

        // resolved = blotters that have NO corresponding case
        // (business rule uses cases.blotter_id FK)
        // Keep the relationship query out of final computation to avoid relationship mismatch.
        $resolvedQuery = (clone $totalQuery)->whereDoesntHave('case');


        // Important: if we later add optional filters, apply them to BOTH totalQuery and resolvedQuery
        // so that the ratio remains consistent.







        if (!empty($filters['incident_type'])) {
            $resolvedQuery->where('incident_type', $filters['incident_type']);
            $totalQuery->where('incident_type', $filters['incident_type']);
        }

        $total = $totalQuery->count();

        // Average resolution time
        $avgResolutionDays = Blotter::whereBetween('created_at', [$dateFrom, $dateTo])
            ->selectRaw('AVG(TIMESTAMPDIFF(DAY, created_at, NOW())) as avg_days')
            ->value('avg_days') ?? 0;

        // Resolution rate (business rule)
        // A blotter is RESOLVED when:
        //  - it has no related case
        //  - OR its related case status is terminal: closed, dismissed, settled
        $resolvedWithoutCase = (clone $totalQuery)->whereDoesntHave('case')->count();

        $resolvedWithTerminalCase = (clone $totalQuery)
            ->whereHas('case', function ($cq) {
                $cq->whereIn('status', ['closed', 'dismissed', 'settled']);
            })
            ->count();

        $blotterResolutionRate = $total > 0
            ? round((($resolvedWithoutCase + $resolvedWithTerminalCase) / $total) * 100)
            : 0;

        // For PDF stats/cards
        $withCase = (clone $totalQuery)->whereHas('case')->count();
        $withoutCase = $total - $withCase;

        // Case conversion rate (blotters that become cases)
        $caseConversionRate = $total > 0 ? round(($withCase / $total) * 100) : 0;



        return compact('incidentTypeStats', 'blotterMonthlyTrends', 'blotterResolutionRate', 'avgResolutionDays', 'caseConversionRate', 'withCase', 'withoutCase');

    }

    private function getCaseAnalytics(string $dateFrom, string $dateTo, array $filters = [])
    {

        $caseQuery = CaseFile::whereBetween('created_at', [$dateFrom, $dateTo]);

        // Apply Peace & Order filter (Case Status)
        if (!empty($filters['case_status'])) {
            $caseQuery->where('status', $filters['case_status']);
        }

        // By status
        $caseStatusStats = (clone $caseQuery)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();


        // By case type (from blotter's incident_type)
        $caseTypeQuery = CaseFile::join('blotters', 'cases.blotter_id', '=', 'blotters.id')
            ->select('blotters.incident_type', DB::raw('COUNT(*) as count'))
            ->whereBetween('blotters.incident_date', [$dateFrom, $dateTo]);




        // Apply Peace & Order filters
        if (!empty($filters['case_status'])) {
            $caseTypeQuery->where('cases.status', $filters['case_status']);
        }

        if (!empty($filters['incident_type'])) {
            $caseTypeQuery->where('blotters.incident_type', $filters['incident_type']);
        }


        $caseTypeStats = $caseTypeQuery
            ->groupBy('blotters.incident_type')
            ->orderByDesc('count')
            ->pluck('count', 'blotters.incident_type')
            ->toArray();



        // Monthly trends
        $caseMonthlyTrends = CaseFile::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->whereBetween('created_at', [$dateFrom, $dateTo])->whereYear('created_at', '=', Carbon::now()->year)
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Resolution rate
        // Business rule: consider a case RESOLVED when status is:
        //   - closed
        //   - dismissed
        //   - settled
        $resolved = CaseFile::whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereIn('status', ['closed', 'dismissed', 'settled'])
            ->count();
        $total = CaseFile::whereBetween('created_at', [$dateFrom, $dateTo])->count();
        $resolutionRate = $total > 0 ? round(($resolved / $total) * 100) : 0;


        // Case aging (average days open)
        $avgDaysOpen = CaseFile::whereBetween('created_at', [$dateFrom, $dateTo])->whereNull('closed_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(DAY, created_at, CURDATE())) as avg_days')
            ->value('avg_days') ?? 0;

        return compact('caseStatusStats', 'caseTypeStats', 'caseMonthlyTrends', 'resolutionRate', 'avgDaysOpen');
    }

    // New: Barangay Officials and Terms Analytics
    private function getOfficialAnalytics(string $dateFrom, string $dateTo)
    {
        // Include only active term officials (per requirement)
        $officialQuery = \App\Models\BarangayOfficial::whereHas('term', function ($q) {
            $q->where('is_active', true)->where('is_archived', false);
        });

        // Total officials (active)
        $totalOfficials = (clone $officialQuery)->count();

        // Officials by position
        $positionStats = (clone $officialQuery)
            ->select('position', DB::raw('COUNT(*) as count'))
            ->groupBy('position')
            ->pluck('count', 'position')
            ->toArray();

        // Officials by committee
        $committeeStats = (clone $officialQuery)
            ->select('committee', DB::raw('COUNT(*) as count'))
            ->groupBy('committee')
            ->pluck('count', 'committee')
            ->toArray();

        // Active terms
        $activeTerms = \App\Models\BarangayTerm::active()->count();
        $archivedTerms = \App\Models\BarangayTerm::archived()->count();

        // Detailed rows for PDF table (members list)
        // Keep payload stable for `resources/views/admin/analytics/pdf.blade.php`.
        $rows = (clone $officialQuery)
            ->with('term')
            ->get()
            ->filter(function ($official) {
                $term = $official->getRelationValue('term');
                return $term && $term->is_active && !$term->is_archived;
            })
            ->map(function ($official) {

                // Avoid using `$official->term` in a way that could trigger dynamic-property errors.
                // Since we eager-loaded `term`, we can safely read it as an object.
                $term = $official->getRelationValue('term');


                $termLabel = null;
                if ($term) {
                    $termLabel = $term->term_label ?? ($term->label ?? null);
                    if (!$termLabel) {
                        $termLabel = (string) ($term->id ?? '');
                    }
                }

                // Query already restricts to active, non-archived terms.
                $status = 'Active';

                return [
                    'name' => $official->name ?? ($official->full_name ?? ($official->user->name ?? ($official->user->full_name ?? 'Unknown'))),
                    'position' => $official->position ?? '—',
                    'committee' => $official->committee ?? '—',
                    'term' => $termLabel ?: '—',
                    'status' => $status,
                ];
            })
            ->values()
            ->toArray();


        // Group officials by active term so UI can render “active terms officials” tables.
        $officialsByTerm = (clone $officialQuery)
            ->with('term')
            ->get()
            ->filter(function ($official) {
                $term = $official->getRelationValue('term');
                return $term && $term->is_active && !$term->is_archived;
            })
            ->groupBy(function ($official) {
                $term = $official->getRelationValue('term');
                return (string) ($term->term_label ?? ($term->title ?? $term->id ?? 'Unknown'));
            })
            ->map(function ($group) {
                return $group->values()->map(function ($official) {
                    $term = $official->getRelationValue('term');
                    return [
                        'name' => $official->name ?? ($official->full_name ?? ($official->user->name ?? ($official->user->full_name ?? 'Unknown'))),
                        'position' => $official->position ?? '—',
                        'committee' => $official->committee ?? '—',
                        'term' => $term?->term_label ?? ($term?->title ?? $term?->id ?? '—'),
                        'status' => 'Active',
                    ];
                })->toArray();
            })
            ->toArray();

        return [
            'totalOfficials' => $totalOfficials,
            'positionStats' => $positionStats,
            'committeeStats' => $committeeStats,
            'activeTerms' => $activeTerms,
            'archivedTerms' => $archivedTerms,
            'rows' => $rows,
            'officialsByTerm' => $officialsByTerm,
        ];

    }


    // New: Announcement and Event Analytics
    private function getAnnouncementEventAnalytics(string $dateFrom, string $dateTo)
    {
        // Total announcements
        $totalAnnouncements = \App\Models\Announcement::whereBetween('created_at', [$dateFrom, $dateTo])->count();
        $publishedAnnouncements = \App\Models\Announcement::where('is_published', true)->whereBetween('created_at', [$dateFrom, $dateTo])->count();
        $announcementTypes = \App\Models\Announcement::whereBetween('created_at', [$dateFrom, $dateTo])->select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')->pluck('count', 'type')->toArray();

        // Total events
        $totalEvents = \App\Models\Event::whereBetween('created_at', [$dateFrom, $dateTo])->count();
        $publishedEvents = \App\Models\Event::where('is_published', true)->whereBetween('created_at', [$dateFrom, $dateTo])->count();
        $eventTypes = \App\Models\Event::whereBetween('created_at', [$dateFrom, $dateTo])->select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')->pluck('count', 'type')->toArray();

        return compact(
            'totalAnnouncements', 'publishedAnnouncements', 'announcementTypes',
            'totalEvents', 'publishedEvents', 'eventTypes'
        );
    }

    // New: User and Administrator Analytics & Audit Logs
    private function getUserAdminAuditAnalytics(string $dateFrom, string $dateTo)
    {
        // User and Admin Analytics
        // FIX: PDF “TOTAL USERS/TOTAL ADMINS” should reflect the actual DB totals,
        // independent of the selected date range (users/admins cards are intended as global totals).
        // Active/Inactive are derived from `users.status`.
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $inactiveUsers = User::where('status', 'inactive')->count();

        // Keep role distribution consistent with the chosen semantics (global role totals).
        $userRoleDistribution = User::select('role', DB::raw('COUNT(*) as count'))
            ->groupBy('role')
            ->pluck('count', 'role')
            ->toArray();

        $totalAdmins = \App\Models\Admin::count();



        // Audit Log Analytics
        $totalAuditLogs = \App\Models\AuditLog::whereBetween('created_at', [$dateFrom, $dateTo])->count();
        $mostActiveUsers = \App\Models\AuditLog::whereBetween('created_at', [$dateFrom, $dateTo])->select('user_id', DB::raw('COUNT(*) as count'))
            ->groupBy('user_id')->orderByDesc('count')->limit(5)->with('user:id,email')->get();
        $mostModifiedModules = \App\Models\AuditLog::whereBetween('created_at', [$dateFrom, $dateTo])->select('module', DB::raw('COUNT(*) as count'))
            ->groupBy('module')->orderByDesc('count')->limit(5)->pluck('count', 'module')->toArray();
        $mostCommonActions = \App\Models\AuditLog::whereBetween('created_at', [$dateFrom, $dateTo])->select('action', DB::raw('COUNT(*) as count'))
            ->groupBy('action')->orderByDesc('count')->limit(5)->pluck('count', 'action')->toArray();

        return compact(
            'totalUsers', 'activeUsers', 'inactiveUsers', 'userRoleDistribution', 'totalAdmins',
            'totalAuditLogs', 'mostActiveUsers', 'mostModifiedModules', 'mostCommonActions'
        );
    }
}
