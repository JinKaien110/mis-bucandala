<?php

namespace App\Http\Controllers\Admin;

use App\Models\Blotter;
use App\Models\CaseFile;
use App\Models\DocumentRequest;
use App\Models\Household;
use App\Models\Resident;
use App\Models\Payment;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AnalyticsService
{
    public function getTotalResidents(): int
    {
        return Resident::count();
    }

    public function getTotalHouseholds(): int
    {
        return Household::count();
    }

    public function getTotalDocumentRequests(): int
    {
        return DocumentRequest::count();
    }

    public function getTotalBlotters(): int
    {
        return Blotter::count();
    }

    public function getTotalCases(): int
    {
        return CaseFile::count();
    }

    public function getTotalUsers(): int
    {
        return User::count();
    }

    public function getGrowthPercentage(string $model, string $period, Carbon $compareDate): int
    {
        $currentCount = $model::when($period === 'last_month', function ($q) use ($compareDate) {
            $q->whereMonth('created_at', '=', $compareDate->month)
                ->whereYear('created_at', '=', $compareDate->year);
        })->count();

        $previousPeriod = $compareDate->copy()->subMonth();
        $previousCount = $model::whereMonth('created_at', '=', $previousPeriod->month)
            ->whereYear('created_at', '=', $previousPeriod->year)
            ->count();

        if ($previousCount === 0) {
            return $currentCount > 0 ? 100 : 0;
        }

        return (int) round((($currentCount - $previousCount) / $previousCount) * 100);
    }

    public function exportAsCsv(array $data, string $dateFrom, string $dateTo)
    {
        $filename = 'analytics-export-' . now()->format('Y-m-d-H-i-s') . '.csv';

        $callback = function () use ($data, $dateFrom, $dateTo) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['Analytics Dashboard Export - Barangay Bucandala 1']);
            fputcsv($file, ['Period:', $dateFrom . ' to ' . $dateTo]);
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
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function exportAsPdf(array $data, string $dateFrom, string $dateTo, array $selectedSections = [], bool $includeRecordTables = false)
    {

        // Only pass through sections requested for PDF.
        // NOTE: Payments can be exported under either `Overview` (legacy) or `Payments` (current key mapping).
        $overview = array_key_exists('Overview', $data) ? ($data['Overview'] ?? []) : [];
        $paymentsPayload = $data['Payments']['paymentAnalytics'] ?? null;


        $residents = $data['Residents'] ?? [];

        // Optional: residents list table for PDF exports (table lists)
        $residentsList = $data['ResidentsList'] ?? [];
        $householdsList = $data['HouseholdsList'] ?? [];

        // If includeRecordTables is enabled (pdf_charts_table_lists), but controller didn't attach ResidentsList,
        // gracefully fall back to an empty array to avoid breaking the PDF template.
        if (!is_array($residentsList)) {
            $residentsList = [];
        }

        $documentsList = $data['DocumentsList'] ?? [];

        if (!is_array($documentsList)) {
            $documentsList = [];
        }

        $paymentsList = $data['PaymentsList'] ?? [];

        if (!is_array($paymentsList)) {
            $paymentsList = [];
        }

        $blottersList = $data['BlottersList'] ?? [];

        if (!is_array($blottersList)) {
            $blottersList = [];
        }

        $casesList = $data['CasesList'] ?? [];

        if (!is_array($casesList)) {
            $casesList = [];
        }

        $announcementsList = $data['AnnouncementsList'] ?? [];
        $eventsList = $data['EventsList'] ?? [];

        if (!is_array($announcementsList)) {
            $announcementsList = [];
        }

        if (!is_array($eventsList)) {
            $eventsList = [];
        }

        $auditLogsList = $data['AuditLogsList'] ?? [];


        $households = $data['Households'] ?? [];
        $documents = $data['Documents'] ?? [];
        $blotters = $data['Blotters'] ?? [];
        $cases = $data['Cases'] ?? [];

        $selectedSet = array_flip($selectedSections);

        // Ensure PDF template section gating keys exist ONLY for selected sections.
        // `resources/views/admin/analytics/pdf.blade.php` renders based on isset($documents['totalDocumentRequests']) etc.
        // Previously we filled these keys from $overview, which caused unselected sections to appear.
        if (isset($selectedSet['documents']) && !isset($documents['totalDocumentRequests'])) {
            $documents['totalDocumentRequests'] = (int) ($overview['totalDocumentRequests'] ?? 0);
        }
        if (isset($selectedSet['blotters']) && !isset($blotters['totalBlotters'])) {
            $blotters['totalBlotters'] = (int) ($overview['totalBlotters'] ?? 0);
        }
        if (isset($selectedSet['households']) && !isset($households['totalHouseholds'])) {
            $households['totalHouseholds'] = (int) ($overview['totalHouseholds'] ?? 0);
        }
        if (isset($selectedSet['cases']) && !isset($cases['totalCases'])) {
            $cases['totalCases'] = (int) ($overview['totalCases'] ?? 0);
        }


        $officials = $data['Officials'] ?? [];
        if (!is_array($officials)) {
            $officials = [];
        }

        // Normalize Officials payload so pdf.blade.php consistently renders
        // when the user selected only "Officials".
        $officials['rows'] = $officials['rows'] ?? [];
        if (!is_array($officials['rows'])) {
            $officials['rows'] = [];
        }

        $officials['totalOfficials'] = (int) ($officials['totalOfficials'] ?? count($officials['rows']));
        $officials['positionStats'] = $officials['positionStats'] ?? [];
        if (!is_array($officials['positionStats'])) {
            $officials['positionStats'] = [];
        }

        $officials['committeeStats'] = $officials['committeeStats'] ?? [];
        if (!is_array($officials['committeeStats'])) {
            $officials['committeeStats'] = [];
        }

        $officials['activeTerms'] = (int) ($officials['activeTerms'] ?? 0);
        $officials['archivedTerms'] = (int) ($officials['archivedTerms'] ?? 0);

        // Announcements & Events
        // AnalyticsService export() receives the already-computed payload under either:
        //  - $data['AnnouncementsEvents'] (selected handler)
        //  - OR nested under $data['AnnouncementsEvents'][...] (legacy/extra nesting)
        // PDF template expects FLAT vars.
if (isset($selectedSet['announcements-events'])) {

    $announcementsEventsRaw = $data['AnnouncementsEvents'] ?? [];
    $announcementsEvents = $announcementsEventsRaw;

    if (
        is_array($announcementsEventsRaw)
        && isset($announcementsEventsRaw['announcementsEvents'])
        && is_array($announcementsEventsRaw['announcementsEvents'])
    ) {
        $announcementsEvents = $announcementsEventsRaw['announcementsEvents'];
    }

    if (!is_array($announcementsEvents)) {
        $announcementsEvents = [];
    }

    $announcementsEvents['totalAnnouncements'] =
        (int)($announcementsEvents['totalAnnouncements'] ?? 0);

    $announcementsEvents['totalEvents'] =
        (int)($announcementsEvents['totalEvents'] ?? 0);

    $announcementTypes = $announcementsEvents['announcementTypes'] ?? [];
    $eventTypes = $announcementsEvents['eventTypes'] ?? [];

} else {

    $announcementsEvents = [];
    $announcementTypes = [];
    $eventTypes = [];

}



        $usersAdminsAudit = $data['UsersAdminsAudit'] ?? [];



        // Some resident aggregations may come back with lowercase/uppercase keys or numeric/associative mix.
        // Normalize to expected 'Male'/'Female' and ensure we pass correct counts.
        $genderStats = $residents['genderStats'] ?? [];

        // Always initialize to avoid compact()/undefined variable issues when exporting subsets.
        $paymentAnalytics = [];

        if (is_array($overview) && isset($overview['paymentAnalytics']) && is_array($overview['paymentAnalytics'])) {
            $paymentAnalytics = $overview['paymentAnalytics'];
        }

        if (!is_array($genderStats)) $genderStats = [];

        $normalizedGender = [
            'Male' => (int)($genderStats['Male'] ?? $genderStats['male'] ?? 0),
            'Female' => (int)($genderStats['Female'] ?? $genderStats['female'] ?? 0),
        ];

        // Also update the $residents array itself so the PDF template
        // (which reads $residents['genderStats']['Male'] / ['Female'])
        // gets the correct uppercase-keyed data from the database
        $residents['genderStats'] = $normalizedGender;

        $ageGroups = $residents['ageGroups'] ?? [];
        if (!is_array($ageGroups)) $ageGroups = [];

        // Resident demographics stats building is intentionally deferred.
        // Blade rendering is gated by presence of $residents payload.
        // If Residents is NOT selected, we will clear $residents and related values.

        // Force chart generation even when keys differ; keep arrays explicit.
        $civilStatusStats = $residents['civilStatusStats'] ?? [];
        if (!is_array($civilStatusStats)) $civilStatusStats = [];

        // Resident classifications chart (PWD / Solo Parent / Indigent / 4Ps)
        // Keep keys aligned with what pdf.blade.php renders.
        $classificationStats = [
            'PWD' => (int)($residents['pwdResidents'] ?? 0),
            'Solo Parent' => (int)($residents['soloParentResidents'] ?? 0),
            'Indigent' => (int)($residents['indigentResidents'] ?? 0),
            '4Ps' => (int)($residents['fourPsBeneficiaries'] ?? 0),
        ];

        // Phase population (A/B/C/No Phase) from residents analytics payload
        $phasePopulation = $residents['phasePopulation'] ?? [];
        if (!is_array($phasePopulation)) $phasePopulation = [];


        // Household charts
        $householdPhaseStats = $households['phaseStats'] ?? [];
        // Additional household analytics requested in PDF
        $indigentHouseholds = (int) ($households['indigentCount'] ?? 0);
        $seniorsHouseholds = (int) ($households['seniorCount'] ?? 0);
        $pwdHouseholds = (int) ($households['pwdCount'] ?? 0);

        if (!is_array($householdPhaseStats)) $householdPhaseStats = [];

        // Normalize household phase stats into consistent labels
        $householdPhaseNormalized = [
            'A' => (int)($householdPhaseStats['A'] ?? 0),
            'B' => (int)($householdPhaseStats['B'] ?? 0),
            'C' => (int)($householdPhaseStats['C'] ?? 0),
            'No Phase' => (int)($householdPhaseStats['No Phase'] ?? 0),
        ];

        $homeownershipStats = $households['homeownershipStats'] ?? [];
        if (!is_array($homeownershipStats)) $homeownershipStats = [];

        // Additional household distribution charts (income range, household type, utilities, disaster risk)
        $incomeRangeStats = $households['incomeRangeStats'] ?? [];
        $householdTypeStats = $households['householdTypeStats'] ?? [];
        $electricityAccess = (int) ($households['electricityAccess'] ?? 0);
        $toiletAccess = (int) ($households['toiletAccess'] ?? 0);
        $bathroomAccess = (int) ($households['bathroomAccess'] ?? 0);
        $kitchenAccess = (int) ($households['kitchenAccess'] ?? 0);
        $garageAccess = (int) ($households['garageAccess'] ?? 0);
        $disasterRiskStats = $households['disasterRiskStats'] ?? [];

        $utilitiesAccessStats = [
            'Electricity' => $electricityAccess,
            'Water' => (int)($households['has_water_access'] ?? ($households['toiletAccess'] ?? $toiletAccess)),
            'Toilet' => $toiletAccess,
            'Bathroom' => $bathroomAccess,
            'Kitchen' => $kitchenAccess,
            'Garage' => $garageAccess,
        ];

        // If water is missing, fallback to 0 (PDF will still render).
        if (!array_key_exists('Water', $utilitiesAccessStats)) {
            $utilitiesAccessStats['Water'] = (int)($households['waterAccess'] ?? 0);
        }

        $charts = [];

        // Resident Demographics charts + payload gating (strict)
        // pdf.blade.php renders the Resident Demographics section using:
        //   $sectionSelectedResidents = !empty($residents) && isset($residents['genderStats']) && isset($residents['ageGroups']);
        // So we must clear ONLY the Resident Demographics payload when Residents is NOT selected.
        if (isset($selectedSet['residents'])) {
            $charts['genderPie'] = $this->generatePieChartSvg($normalizedGender, ['Male' => '#3b82f6', 'Female' => '#ec4899'], true, true);
            $charts['ageBar'] = $this->generateBarChartSvg($ageGroups, 'Age Distribution');
            $charts['civilStatusPie'] = $this->generatePieChartSvg($civilStatusStats, ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'], true, true);
            $charts['classificationBar'] = $this->generateBarChartSvg($classificationStats, 'Resident Classifications', 180);
            $charts['phasePie'] = $this->generatePieChartSvg($phasePopulation, ['#8b5cf6', '#3b82f6', '#10b981', '#f59e0b', '#ef4444'], true, true);
            $charts['occupationBar'] = $this->generateBarChartSvg($residents['occupationStats'] ?? [], 'Top Occupations', 200);

            // If upstream sends empty Residents payload while checkbox is selected, create minimal payload
            // so Blade gating still renders.
            if (empty($residents)) {
                $residents = [
                    'genderStats' => $normalizedGender,
                    'ageGroups' => $ageGroups,
                    'civilStatusStats' => $civilStatusStats,
                    'occupationStats' => [],
                    'pwdResidents' => 0,
                    'soloParentResidents' => 0,
                    'indigentResidents' => 0,
                    'fourPsBeneficiaries' => 0,
                    'phasePopulation' => $phasePopulation,
                ];
            }
        } else {
            // Residents NOT selected => make sure Blade condition cannot pass.
            $residents = [];
            $charts['genderPie'] = '';
            $charts['ageBar'] = '';
            $charts['civilStatusPie'] = '';
            $charts['classificationBar'] = '';
            $charts['phasePie'] = '';
            $charts['occupationBar'] = '';
        }




        // Household analytics charts (only if Households selected)
        if (isset($selectedSet['households'])) {
            $charts['householdPhasePie'] = $this->generatePieChartSvg($householdPhaseNormalized, ['#8b5cf6', '#3b82f6', '#10b981', '#f59e0b'], true, true);
            $charts['homeownershipBar'] = $this->generateBarChartSvg($homeownershipStats, 'Homeownership Distribution', 180);
            $charts['incomeRangePie'] = $this->generatePieChartSvg($incomeRangeStats, ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'], true, true);
            $charts['householdTypePie'] = $this->generatePieChartSvg($householdTypeStats, ['#06b6d4', '#3b82f6', '#10b981', '#f59e0b', '#ef4444'], true, true);
            $charts['utilitiesAccessBar'] = $this->generateBarChartSvg($utilitiesAccessStats, 'Utilities Access', 140);
            $charts['disasterRiskPie'] = $this->generatePieChartSvg($disasterRiskStats, ['#ef4444', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6'], true, true);
        }

        // Documents charts (only if Documents selected)
        if (isset($selectedSet['documents'])) {
            $charts['docTypePie'] = $this->generatePieChartSvg($documents['topTypes'] ?? [], ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6'], true, true);
        }

        // Blotters charts (only if Blotters selected)
        if (isset($selectedSet['blotters'])) {
            $charts['incidentPie'] = $this->generatePieChartSvg(array_slice($blotters['incidentTypeStats'] ?? [], 0, 5), ['#ef4444', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6'], true, true);
        }

        // Cases charts (only if Cases selected)
        if (isset($selectedSet['cases'])) {
            $caseStatusStats = $cases['caseStatusStats'] ?? [];
            $caseTypeStats = $cases['caseTypeStats'] ?? [];
            $caseMonthlyTrends = $cases['caseMonthlyTrends'] ?? [];

            if (!is_array($caseStatusStats)) $caseStatusStats = [];
            if (!is_array($caseTypeStats)) $caseTypeStats = [];
            if (!is_array($caseMonthlyTrends)) $caseMonthlyTrends = [];

            // PDF template expects these SVG keys:
            // - caseStatusPie
            // - caseTypePie
            // - caseMonthlyTrendArea
            $charts['caseStatusPie'] = $this->generatePieChartSvg($caseStatusStats, ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'], true, true);
            $charts['caseTypePie'] = $this->generatePieChartSvg($caseTypeStats, ['#8b5cf6', '#3b82f6', '#10b981', '#f59e0b', '#ef4444'], true, true);

            // Monthly trends in dashboard are bar/area; in PDF we render as bar SVG.
            // Template uses img for `caseMonthlyTrendArea`, so we supply bar SVG.
            // Ensure x-axis labels are month names (Jan..Dec) instead of numeric month values.
            $monthMap = [
                1  => 'Jan',
                2  => 'Feb',
                3  => 'Mar',
                4  => 'Apr',
                5  => 'May',
                6  => 'Jun',
                7  => 'Jul',
                8  => 'Aug',
                9  => 'Sep',
                10 => 'Oct',
                11 => 'Nov',
                12 => 'Dec',
            ];

            $normalizedCaseMonthly = [];
            foreach ($monthMap as $mNum => $mName) {
                // DB returns MONTH(created_at) => numeric month key (1..12)
                $normalizedCaseMonthly[$mName] = isset($caseMonthlyTrends[$mNum]) ? (float)$caseMonthlyTrends[$mNum] : 0;
            }

            // Preserve numeric ordering by using the explicit monthMap order.
            $charts['caseMonthlyTrendArea'] = $this->generateBarChartSvg($normalizedCaseMonthly, 'Monthly Case Trends', 180);
        }



        // Officials charts (only if Officials selected)
        if (isset($selectedSet['officials'])) {
            // Expect $positionStats / $committeeStats from $data['Officials'] but exportAsPdf() currently doesn't map them.
            // AnalyticsController->exportAsPdf passes the entire Officials payload as $officials.
            $positionStats = $officials['positionStats'] ?? [];
            $committeeStats = $officials['committeeStats'] ?? [];

            $charts['officialPositionBar'] = $this->generateBarChartSvg($positionStats, 'Officials by Position', 180);
            $charts['officialCommitteePie'] = $this->generatePieChartSvg($committeeStats, ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'], true, true);
        }

        // Announcements & Events charts (only if Announcements & Events selected)
        if (isset($selectedSet['announcements-events'])) {
            $announcementTypes = $announcementsEvents['announcementTypes'] ?? [];
            $eventTypes = $announcementsEvents['eventTypes'] ?? [];

            $charts['announcementTypePie'] = $this->generatePieChartSvg($announcementTypes, ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'], true, true);
            $charts['eventTypeBar'] = $this->generateBarChartSvg($eventTypes, 'Event Types', 180);
        }

        // Users & Audit charts (only if Users & Audit selected)
        if (isset($selectedSet['users-audit'])) {
            $userRoleDistribution = $usersAdminsAudit['userRoleDistribution'] ?? [];
            $mostCommonActions = $usersAdminsAudit['mostCommonActions'] ?? [];

            $charts['userRolePie'] = $this->generatePieChartSvg($userRoleDistribution, ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'], true, true);
            $charts['auditActionDonut'] = $this->generatePieChartSvg($mostCommonActions, ['#8b5cf6', '#6b7280', '#f59e0b', '#ef4444', '#10b981'], true, true);
        }
      

        // Payments charts (only if Payments selected)
        if (isset($selectedSet['payments'])) {
            // Build a normalized payments analytics payload from either possible export keys.
            $paymentAnalytics = [];
            if (!empty($overview) && isset($overview['paymentAnalytics']) && is_array($overview['paymentAnalytics'])) {
                $paymentAnalytics = $overview['paymentAnalytics'];
            } elseif ($paymentsPayload !== null && is_array($paymentsPayload)) {
                // Controller exports payments under `$data['Payments']['paymentAnalytics']`.
                $paymentAnalytics = $paymentsPayload;
            }

            
            $statusChart = $paymentAnalytics['statusChart'] ?? [];
            $monthlyRevenue = $paymentAnalytics['monthlyRevenue'] ?? [];

            // Ensure we always pass all 12 months to the PDF bar chart.
            // getPaymentAnalytics() already returns Jan..Dec keys, but keep this defensive.
            $monthMap = [
    1  => 'Jan',
    2  => 'Feb',
    3  => 'Mar',
    4  => 'Apr',
    5  => 'May',
    6  => 'Jun',
    7  => 'Jul',
    8  => 'Aug',
    9  => 'Sep',
    10 => 'Oct',
    11 => 'Nov',
    12 => 'Dec',
];

$normalizedMonthlyRevenue = [];

foreach ($monthMap as $monthNumber => $monthName) {
    $normalizedMonthlyRevenue[$monthName] = isset($monthlyRevenue[$monthNumber])
        ? (float) $monthlyRevenue[$monthNumber]
        : 0.0;
}

$monthlyRevenue = $normalizedMonthlyRevenue;
            $charts['paymentStatusChart'] = $this->generatePieChartSvg($statusChart, ['#06b6d4', '#3b82f6', '#10b981', '#f59e0b', '#ef4444'], true, true);
            // Use bar chart for monthly revenue in PDF.
            $charts['monthlyRevenueBar'] = $this->generateBarChartSvg($monthlyRevenue, 'Monthly Revenue', 180);
            // Backward compatibility (in case template still references it somewhere).
            $charts['monthlyRevenueArea'] = $charts['monthlyRevenueBar'];

        }

        // Note: pdf.blade.php uses additional chart keys for new sections, generated above.




        // --- PDF chart debug trail (SVG length + preview + optional dump) ---
        $debugDir = storage_path('app/debug_charts');
        try {
            if (!is_dir($debugDir)) {
                mkdir($debugDir, 0775, true);
            }
        } catch (\Throwable $e) {
            // ignore dump errors; logging still works
        }

        foreach ($charts as $chartKey => $svg) {
            $svgStr = is_string($svg) ? $svg : '';
            $len = strlen($svgStr);
            $preview = $len > 0 ? Str::limit(preg_replace('/\s+/', ' ', trim($svgStr)), 220, '...') : '';

            Log::info('[AnalyticsPDF] chart_svg', [
                'key' => $chartKey,
                'length' => $len,
                'preview' => $preview,
            ]);

            // Dump SVG for isolation (open directly in browser)
            if ($len > 0) {
                try {
                    // Keep filename stable even if chart keys are duplicated/changed.
                    $safeKey = preg_replace('/[^A-Za-z0-9_\-]/', '_', (string) $chartKey);
                    file_put_contents($debugDir . DIRECTORY_SEPARATOR . $safeKey . '.svg', $svgStr);
                } catch (\Throwable $e) {
                    // ignore dump errors
                }
            }
        }
        // ---------------------------------------------------------------------

        $html = view('admin.analytics.pdf', compact(
    'dateFrom',
    'dateTo',
    'overview',
    'residents',
    'residentsList',
    'households',
    'householdsList',
    'documentsList',
    'paymentsList',
    'blottersList',
    'casesList',
    'eventsList',
    'auditLogsList',
    'announcementsList',
    'documents',
    'blotters',
    'cases',
    'officials',
    'announcementsEvents',
    'usersAdminsAudit',
    'charts',
    'paymentAnalytics',
    'includeRecordTables'
));

        // If user exported ONLY payments, pdf.blade.php payments section is gated by the presence
        // of $paymentAnalytics/payments payload keys and chart monthlyRevenueBar.
        // For safety, ensure monthlyRevenueBar exists even if upstream payload month keys are empty.
        // (removed) redundant monthlyRevenueBar fallback after debug dump.
        // It could reference an uninitialized $paymentAnalytics variable when exporting only payments.


        $dompdf = Pdf::loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'analytics-report-' . now()->format('Y-m-d-H-i-s') . '.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function writeCsvArray($file, array $data, int $indent = 0): void
    {
        foreach ($data as $key => $value) {
            $prefix = str_repeat('  ', $indent);

            if (is_array($value)) {
                fputcsv($file, [$prefix . $key . ':']);
                $this->writeCsvArray($file, $value, $indent + 1);
            } elseif (is_numeric($value)) {
                fputcsv($file, [$prefix . $key, number_format($value)]);
            } else {
                fputcsv($file, [$prefix . $key, $value]);
            }
        }
    }


    public function getPaymentAnalytics(?string $dateFrom = null, ?string $dateTo = null)
    {

        $paidTransactions = Payment::where('status', 'success')->count();
        $unpaidTransactions = Payment::where('status', '!=', 'success')->count();

        $totalCollections = Payment::where('status', 'success')->sum('amount');
        $totalTransactions = Payment::count();

        $averagePayment = Payment::where('status', 'success')->avg('amount');

        $months = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Aug',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec',
        ];

        // Always include all months in the payload, even when SUM(amount) is 0.
        // This is required so the SVG bar chart renders all 12 X-axis labels.
        //
        // NOTE: We keep the DB query numeric for grouping (1..12), but we map the final payload
        // to month names (Jan..Dec) using the $months mapping above.
        $monthlyRevenueRows = Payment::selectRaw(
            "MONTH(COALESCE(paid_at, created_at)) as month, SUM(amount) as total"
        )
            ->where('status', 'success')
            ->when($dateFrom && $dateTo, function ($q) use ($dateFrom, $dateTo) {
                $q->whereRaw('COALESCE(paid_at, created_at) BETWEEN ? AND ?', [$dateFrom, $dateTo]);
            }, function ($q) {
                // No explicit date window provided: default to current year
                $q->whereYear(DB::raw('COALESCE(paid_at, created_at)'), Carbon::now()->year);
            })

            ->groupBy('month')
            ->orderBy('month', 'asc')


            ->pluck('total', 'month')
            ->toArray();

        // DEBUG: ensure month grouping is working; keep safe fallback
        // (If DB has no successful payments, values will all remain 0.)

        // Frontend monthly trend expects numeric month keys (1..12) to match fillMonthlyData().
        $monthlyRevenue = [];
        foreach ($months as $monthNum => $monthLabel) {
            $monthlyRevenue[$monthNum] = isset($monthlyRevenueRows[$monthNum]) ? (float)$monthlyRevenueRows[$monthNum] : 0.0;
        }


        // Ensure the bar chart always has 12 labels in correct order.
        // If array keys are altered upstream, reindex explicitly by month name order.
        $monthlyRevenue = array_replace([], $monthlyRevenue);


        $paymentStatusChart = [
            'Paid' => (int) $paidTransactions,
            'Unpaid' => (int) $unpaidTransactions,
        ];

      
        return [
            'paymentAnalytics' => [
                'totalCollections' => $totalCollections,
                'paidTransactions' => $paidTransactions,
                'unpaidTransactions' => $unpaidTransactions,
                'totalTransactions' => $totalTransactions,
                'averagePayment' => $averagePayment,
                'monthlyRevenue' => $monthlyRevenue,
                'statusChart' => $paymentStatusChart,
            ]
        ];
    }

    private function generatePieChartSvg(array $data, array $colors = [], bool $showLegend = false, bool $showValues = false): string
    {
        // Only improve SVG generation for pie charts; keep calculations intact.
        $data = array_filter($data, fn($v) => is_numeric($v) && (int) $v > 0);
        if (empty($data)) {
            return '';
        }

        $total = array_sum($data);
        if ($total <= 0) {
            return '';
        }

        // Normalize color palette into an indexed list.
        if (array_keys($colors) === range(0, count($colors) - 1)) {
            $colorList = array_values($colors);
        } else {
            $colorList = array_values($colors);
        }

        $slices = [];
        $i = 0;
        foreach ($data as $label => $value) {
            $value = (float) $value;
            if ($value <= 0) continue;

            $percent = $value / $total;
            $color = $colorList[$i % max(1, count($colorList))] ?? sprintf('#%06x', random_int(0, 0xFFFFFF));

            $slices[] = [
                'label' => (string) $label,
                'value' => $value,
                'percent' => $percent,
                'color' => $color,
            ];
            $i++;
        }

        if (empty($slices)) {
            return '';
        }

        // Canvas: reserve right side legend (~250px).
        $pieCx = 170;
$pieCy = 170;
$pieR = 120;          // bigger pie
$legendW = 420;       // wider legend area
$pieW = 360;          // more room for the pie
$legendX = $pieW + 20;

        // Layout typography.
        $fontFamily = 'Arial, Helvetica, sans-serif';
        $legendTitleSize = 18; // unused (no title inside SVG), but kept as spec reference
        $legendCategorySize = 13;
        $legendValueSize = 12;
        $legendTextFill = '#333';

        // Legend rows: show all categories; adjust spacing based on count.
        $catCount = count($slices);
        $rowH = 39;
        $startLegendY = 50;

        $legendTotalH = $rowH * $catCount + 10;
       $svgH = max(380, $legendTotalH);
$svgW = $pieW + $legendW;

        $svg = '<svg width="' . $svgW . '" height="' . $svgH . '" viewBox="0 0 ' . $svgW . ' ' . $svgH . '" xmlns="http://www.w3.org/2000/svg">';
        $svg .= '<rect x="0" y="0" width="' . $svgW . '" height="' . $svgH . '" fill="white"/>';

        // Draw pie slices.
        $startAngle = -90;
        foreach ($slices as $idx => $slice) {
            $value = (float) $slice['value'];
            if ($value <= 0) continue;

            $percent = (float) $slice['percent'];
            $endAngle = $startAngle + ($percent * 360);

            $largeArc = ($endAngle - $startAngle) > 180 ? 1 : 0;

            $startRad = deg2rad($startAngle);
            $endRad = deg2rad($endAngle);

            $x1 = $pieCx + $pieR * cos($startRad);
            $y1 = $pieCy + $pieR * sin($startRad);
            $x2 = $pieCx + $pieR * cos($endRad);
            $y2 = $pieCy + $pieR * sin($endRad);

            $path = "M {$pieCx} {$pieCy} L {$x1} {$y1} A {$pieR} {$pieR} 0 {$largeArc} 1 {$x2} {$y2} Z";
            $svg .= "<path d=\"{$path}\" fill=\"{$slice['color']}\" opacity=\"0.9\"/>";

            // Internal slice label: only if > 8%.
            $pctRounded = (int) round($percent * 100);
            if ($showValues && $pctRounded >= 8) {
                // Mid-angle for label placement.
                $midAngle = ($startAngle + $endAngle) / 2;
                $midRad = deg2rad($midAngle);

                // Place label slightly inside the slice.
                $labelR = $pieR * 0.55;
                $lx = $pieCx + $labelR * cos($midRad);
                $ly = $pieCy + $labelR * sin($midRad);

                $svg .= '<text x="' . $lx . '" y="' . ($ly + 4) . '" font-size="12" fill="#111" font-family="' . $fontFamily . '" text-anchor="middle">' . $pctRounded . '%</text>';
            }

            $startAngle = $endAngle;
        }

        // Donut hole to match existing look (white center).
        $svg .= '<circle cx="' . $pieCx . '" cy="' . $pieCy . '" r="38" fill="white"/>';

        // Legend on the right.
        $itemsPerColumn = (int) ceil(count($slices) / 2);

foreach ($slices as $idx => $slice) {

    $column = (int) floor($idx / $itemsPerColumn);
    $row = $idx % $itemsPerColumn;

    $legendItemX = $legendX + ($column * 180);   // distance between columns
    $rowY = $startLegendY + ($row * $rowH);

    $pctRounded = (int) round(((float) $slice['value']) / $total * 100);
    $countRounded = (int) round((float) $slice['value']);

    $markerX = $legendItemX;
    $markerY = $rowY - 9;

    $svg .= '<rect x="' . $markerX . '" y="' . $markerY . '" width="12" height="12" fill="' . $slice['color'] . '"/>';

    $textX = $markerX + 18;

    $svg .= '<text x="' . $textX . '" y="' . $rowY . '" font-size="' . $legendCategorySize . '" fill="' . $legendTextFill . '" font-family="' . $fontFamily . '">' . htmlspecialchars($slice['label'], ENT_QUOTES) . '</text>';

    if ($showValues) {
        $valueText = $countRounded . ' (' . $pctRounded . '%)';
    } else {
        $valueText = $pctRounded . '%';
    }

    $svg .= '<text x="' . $textX . '" y="' . ($rowY + 16) . '" font-size="' . $legendValueSize . '" fill="' . $legendTextFill . '" font-family="' . $fontFamily . '">' . htmlspecialchars($valueText, ENT_QUOTES) . '</text>';
}

        $svg .= '</svg>';
        return $svg;
    }



    private function generateBarChartSvg(array $data, string $title = '', int $height = 150): string
    {
        // Improved DomPDF-friendly SVG bar chart (axes, grid, value labels, professional typography)
        // NOTE: This method returns SVG markup as a string.
        $data = array_filter($data, fn($v) => is_numeric($v) && (float)$v >= 0);
        if (empty($data)) {
            return '';
        }

        // Keep original order of labels as provided.
        $labels = array_keys($data);
        $values = array_values($data);

        $maxValue = max(array_map(fn($v) => (float)$v, $values));
        if ($maxValue <= 0) {
            $maxValue = 1;
        }

        $count = count($values);
        $svgW = 900;
        $svgH = 500;

        // Suggested margins
        $leftMargin = 90;
        $rightMargin = 40;
        $topMargin = 60;
        $bottomMargin = 100;

        // If the dataset is large, tighten bottom area to fit labels (still no truncation; we wrap).
        $labelBand = $bottomMargin;
        if ($count >= 18) {
            $labelBand = 95;
        } elseif ($count >= 12) {
            $labelBand = 90;
        }

        $plotW = $svgW - $leftMargin - $rightMargin;
        $plotH = $svgH - $topMargin - $labelBand;

        // Category spacing (supports 2..20+)
        // Special-case: monthly revenue trend expects ALL months; make bars narrower so the X-axis can fit 12+ labels.
        $isMonthlyRevenueTrend = preg_match('/monthly revenue/i', $title) === 1;

        $gap = $isMonthlyRevenueTrend ? 6 : 10;
        $rawSlot = $plotW / max(1, $count);
        $barW = (int) max(6, min($isMonthlyRevenueTrend ? 45 : 60, floor($rawSlot - $gap)));
        $slotW = $barW + $gap;
        $totalBarsW = $slotW * $count;
        $startX = $leftMargin + (int) max(0, floor(($plotW - $totalBarsW) / 2));

        // For monthly trend we must ensure category labels (month names) are visible.
        // If DomPDF collapses or clips the x-label band, fall back to shorter labels.
        // (We still never show numeric indices.)
        if ($isMonthlyRevenueTrend) {
            $labels = array_map(function ($lbl) {
                $s = trim((string)$lbl);
                return $s !== '' ? $s : '—';
            }, $labels);
        }



        // Heuristic: money formatting if title looks like revenue/collections/payment.
        $isMoney = preg_match('/revenue|payment|collections|amount|php/i', $title) === 1;

        // Monthly Revenue Trend should always use PHP-like ticks even if the title differs.
        // Ensure the generator uses the money tick behavior for monthly revenue bars.
        if (preg_match('/monthly\s*revenue/i', $title) === 1) {
            $isMoney = true;
        }

        // Y ticks (clean interval)

        // For revenue/payment charts we want more granular and readable tick spacing.
        // This avoids cramped Y-axis labels (e.g. when values are in the hundreds/thousands).
        $tickCount = $isMoney ? 8 : 6; // including 0
        $niceMax = $this->niceCeil($maxValue, $tickCount);
        $tickStep = $niceMax / $tickCount;


        $fontFamily = 'Arial, Helvetica, sans-serif';

        // Palette (preserve current palette feel)
        $colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16'];


        $currencySymbol = 'PHP';

        // Legacy fix: some exports may still want axis label/value currency markers normalized.
        // This bar chart generator uses $currencySymbol for formatting; keep it as 'PHP' for the Y-axis
        // labels and value labels.



        // Axis titles
        // If this is a money chart, show PHP on the Y-axis title.
            $yAxisTitle = $isMoney ? 'PHP' : 'Count';
        $xAxisTitle = 'Category';

        $svg = '<svg width="' . $svgW . '" height="' . $svgH . '" viewBox="0 0 ' . $svgW . ' ' . $svgH . '" xmlns="http://www.w3.org/2000/svg">';
        $svg .= '<rect x="0" y="0" width="' . $svgW . '" height="' . $svgH . '" fill="white"/>';

        // Title (18px bold)
        $safeTitle = htmlspecialchars($title ?: '', ENT_QUOTES);
        if ($safeTitle !== '') {
            $svg .= '<text x="' . ($svgW / 2) . '" y="30" font-size="18" font-weight="700" fill="#111" text-anchor="middle" font-family="' . $fontFamily . '">' . $safeTitle . '</text>';
        }

        // Plot area background
        $svg .= '<rect x="' . $leftMargin . '" y="' . $topMargin . '" width="' . $plotW . '" height="' . $plotH . '" fill="transparent"/>';

        // Grid lines + Y axis ticks
        $axisColor = '#111';
        $gridColor = '#e5e7eb';
        $tickFontSize = 11;

        for ($t = 0; $t <= $tickCount; $t++) {
            $val = $t * $tickStep;
            $y = $topMargin + ($plotH - (($val / $niceMax) * $plotH));

            // Grid line
            $svg .= '<line x1="' . $leftMargin . '" y1="' . $y . '" x2="' . ($leftMargin + $plotW) . '" y2="' . $y . '" stroke="' . $gridColor . '" stroke-width="1"/>';

            // Tick label
            $text = $isMoney ? $this->formatMoney((float)$val, $currencySymbol) : $this->formatNumberCompact((float)$val);
            $svg .= '<text x="' . ($leftMargin - 10) . '" y="' . ($y + 4) . '" font-size="' . $tickFontSize . '" fill="#374151" text-anchor="end" font-family="' . $fontFamily . '">' . htmlspecialchars($text, ENT_QUOTES) . '</text>';
        }

        // Axes lines
        $svg .= '<line x1="' . $leftMargin . '" y1="' . $topMargin . '" x2="' . $leftMargin . '" y2="' . ($topMargin + $plotH) . '" stroke="' . $axisColor . '" stroke-width="1"/>';
        $svg .= '<line x1="' . $leftMargin . '" y1="' . ($topMargin + $plotH) . '" x2="' . ($leftMargin + $plotW) . '" y2="' . ($topMargin + $plotH) . '" stroke="' . $axisColor . '" stroke-width="1"/>';

        // Axis titles
        $axisTitleSize = 12;
        // Y axis title rotated
        $svg .= '<text x="' . ($leftMargin - 55) . '" y="' . ($topMargin + ($plotH / 2)) . '" font-size="' . $axisTitleSize . '" font-weight="700" fill="#111" text-anchor="middle" font-family="' . $fontFamily . '" transform="rotate(-90 ' . ($leftMargin - 55) . ' ' . ($topMargin + ($plotH / 2)) . ')">' . htmlspecialchars($yAxisTitle, ENT_QUOTES) . '</text>';

        // X axis title
        $svg .= '<text x="' . ($leftMargin + ($plotW / 2)) . '" y="' . ($topMargin + $plotH + 65) . '" font-size="' . $axisTitleSize . '" font-weight="700" fill="#111" text-anchor="middle" font-family="' . $fontFamily . '">' . htmlspecialchars($xAxisTitle, ENT_QUOTES) . '</text>';

        // Bars + labels
        $barValueFontSize = 11;
        $barValueFontWeight = 700;

        for ($i = 0; $i < $count; $i++) {
            $label = (string)$labels[$i];
            $value = (float)$values[$i];
            // For monthly revenue trend: hide zero/empty months VALUE text (no “PHP0”),
            // but keep the month label visible on the X-axis.
            // We still draw the (0-height) bar, but we skip rendering its value label.
            $shouldRenderMonthValue = !($isMonthlyRevenueTrend && $value <= 0);



            $barHeight = ($value / $niceMax) * $plotH;
            $x = $startX + ($i * $slotW);
            $y = $topMargin + ($plotH - $barHeight);

            $color = $colors[$i % count($colors)];
            $rx = (int) max(4, floor($barW / 6));

            // Bar
            $svg .= '<rect x="' . $x . '" y="' . $y . '" width="' . $barW . '" height="' . $barHeight . '" fill="' . $color . '" rx="' . $rx . '" ry="' . $rx . '"/>';

            // Value label above bar (centered) - optionally hide zeros for monthly revenue.
            if ($shouldRenderMonthValue) {
                $labelText = $isMoney ? $this->formatMoney($value, $currencySymbol) : $this->formatNumberCompact($value);
                $labelY = $y - 8;
                // Keep label within bounds
                if ($labelY < $topMargin + 10) {
                    $labelY = $topMargin + 10;
                }
                $svg .= '<text x="' . ($x + ($barW / 2)) . '" y="' . $labelY . '" font-size="' . $barValueFontSize . '" font-weight="' . $barValueFontWeight . '" fill="#111" text-anchor="middle" font-family="' . $fontFamily . '">' . htmlspecialchars($labelText, ENT_QUOTES) . '</text>';
            }


            // X axis category labels under bars: wrap into multiple lines (never truncate)
            // IMPORTANT: DomPDF sometimes clips labels that extend beyond the SVG canvas.
            // To avoid missing months entirely, place labels using absolute Y positions within
            // the reserved label band (and use a slightly smaller font for monthly trend).
            $catYTop = $topMargin + $plotH + 15;

            $xWrapMaxChars = $isMonthlyRevenueTrend ? 9 : 12;
            $xLineH = $isMonthlyRevenueTrend ? 10 : 12;
            $xFontSize = $isMonthlyRevenueTrend ? 9 : 10;

            $wrapped = $this->wrapLabel($label, $xWrapMaxChars);
            $lineCount = count($wrapped);
            $blockH = $lineCount * $xLineH;
            $startLineY = $catYTop + (max(0, ($labelBand - 15 - $blockH) / 2));


            for ($li = 0; $li < $lineCount; $li++) {
                $svg .= '<text x="' . ($x + ($barW / 2)) . '" y="' . ($startLineY + ($li * $xLineH)) . '" font-size="10" fill="#374151" text-anchor="middle" font-family="' . $fontFamily . '">' . htmlspecialchars($wrapped[$li], ENT_QUOTES) . '</text>';
            }

        }

        $svg .= '</svg>';
        return $svg;
    }

    private function niceCeil(float $maxValue, int $tickCount): float
    {
        if ($maxValue <= 0) return 1;
        $rawStep = $maxValue / $tickCount;
        $pow10 = pow(10, floor(log10($rawStep)));
        $normalized = $rawStep / $pow10;

        // 1, 2, 2.5, 5, 10
        if ($normalized <= 1) $niceNormalized = 1;
        elseif ($normalized <= 2) $niceNormalized = 2;
        elseif ($normalized <= 2.5) $niceNormalized = 2.5;
        elseif ($normalized <= 5) $niceNormalized = 5;
        else $niceNormalized = 10;

        $niceStep = $niceNormalized * $pow10;
        return $niceStep * $tickCount;
    }

    private function formatMoney(float $value, string $symbol = '₱'): string
    {
        // Round to 0 decimals for counts-like revenue; keep 2 decimals if needed.
        $rounded = (abs($value - round($value)) < 0.00001) ? (int)round($value) : round($value, 2);
        if (is_int($rounded)) {
            return $symbol . number_format($rounded, 0);
        }
        return $symbol . number_format($rounded, 2);
    }

    private function formatNumberCompact(float $value): string
    {
        // For ints: normal formatting. For floats: trim to 1..2 decimals.
        if (abs($value - round($value)) < 0.00001) {
            return number_format((int)round($value), 0);
        }
        return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
    }

    private function wrapLabel(string $label, int $maxCharsPerLine): array
    {
        $label = trim($label);
        if ($label === '') return [''];

        // If the label is short, single line.
        if (mb_strlen($label) <= $maxCharsPerLine) {
            return [$label];
        }

        // Break by spaces first.
        $words = preg_split('/\s+/', $label);
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            if ($current === '') {
                $current = $word;
                continue;
            }

            if (mb_strlen($current . ' ' . $word) <= $maxCharsPerLine) {
                $current .= ' ' . $word;
            } else {
                $lines[] = $current;
                $current = $word;
            }
        }

        if ($current !== '') {
            $lines[] = $current;
        }

        // If a single word is too long, hard-split it.
        $final = [];
        foreach ($lines as $ln) {
            if (mb_strlen($ln) <= $maxCharsPerLine) {
                $final[] = $ln;
                continue;
            }
            $hard = '';
            $len = mb_strlen($ln);
            for ($i = 0; $i < $len; $i += $maxCharsPerLine) {
                $final[] = mb_substr($ln, $i, $maxCharsPerLine);
            }
        }

        return $final;
    }

}

