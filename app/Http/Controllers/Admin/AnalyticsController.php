<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blotter;
use App\Models\CaseFile;
use App\Models\DocumentRequest;
use App\Models\DocumentType;
use App\Models\Household;
use App\Models\Pet;
use App\Models\Resident;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->date_from ?? Carbon::now()->startOfYear()->toDateString();
        $dateTo = $request->date_to ?? Carbon::now()->toDateString();

        return view('admin.analytics.index', array_merge(
            $this->getOverviewMetrics(),
            $this->getResidentAnalytics($dateFrom, $dateTo),
            $this->getHouseholdAnalytics(),
            $this->getDocumentRequestAnalytics($dateFrom, $dateTo),
            $this->getBlotterAnalytics($dateFrom, $dateTo),
            $this->getCaseAnalytics($dateFrom, $dateTo),
            $this->getPetAnalytics(),
            compact('dateFrom', 'dateTo')
        ));
    }

    public function export(Request $request)
    {
        $dateFrom = $request->date_from ?? Carbon::now()->startOfYear()->toDateString();
        $dateTo = $request->date_to ?? Carbon::now()->toDateString();
        $format = $request->format ?? 'csv';

        // Gather all analytics data
        $data = array_merge(
            ['Overview' => $this->getOverviewMetrics()],
            ['Residents' => $this->getResidentAnalytics($dateFrom, $dateTo)],
            ['Households' => $this->getHouseholdAnalytics()],
            ['Documents' => $this->getDocumentRequestAnalytics($dateFrom, $dateTo)],
            ['Blotters' => $this->getBlotterAnalytics($dateFrom, $dateTo)],
            ['Cases' => $this->getCaseAnalytics($dateFrom, $dateTo)],
            ['Pets' => $this->getPetAnalytics()],
        );

        if ($format === 'csv') {
            return $this->exportAsCsv($data, $dateFrom, $dateTo);
        }

        if ($format === 'pdf') {
            return $this->exportAsPdf($data, $dateFrom, $dateTo);
        }

        return response()->json(['message' => 'JSON export not implemented yet'], 501);
    }

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
    }

    private function exportAsPdf(array $data, string $dateFrom, string $dateTo)
    {
        $overview = $data['Overview'];
        $residents = $data['Residents'];
        $households = $data['Households'];
        $documents = $data['Documents'];
        $blotters = $data['Blotters'];
        $cases = $data['Cases'];
        $pets = $data['Pets'];

        $charts = [
            'genderPie' => $this->generatePieChartSvg($residents['genderStats'] ?? [], ['Male' => '#3b82f6', 'Female' => '#ec4899']),
            'ageBar' => $this->generateBarChartSvg($residents['ageGroups'] ?? [], 'Age Distribution'),
            'occupationBar' => $this->generateBarChartSvg($residents['occupationStats'] ?? [], 'Top Occupations', 200),
            'docTypePie' => $this->generatePieChartSvg($documents['topTypes'] ?? [], ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6']),
            'incidentPie' => $this->generatePieChartSvg(array_slice($blotters['incidentTypeStats'] ?? [], 0, 5), ['#ef4444', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6']),
            'petTypePie' => $this->generatePieChartSvg($pets['typeStats'] ?? [], ['#3b82f6', '#10b981', '#f59e0b']),
            'vaxPie' => $this->generatePieChartSvg($pets['vaccinationStats'] ?? [], ['#10b981' => 'Up to Date', 'overdue' => '#f59e0b', 'not-vaccinated' => '#ef4444']),
        ];

        $html = view('admin.analytics.pdf', compact(
            'dateFrom', 'dateTo', 'overview', 'residents', 'households',
            'documents', 'blotters', 'cases', 'pets', 'charts'
        ))->render();

        $dompdf = app('dompdf');
        $dompdf->loadHtml($html);
        $dompdf->render();

        $filename = 'analytics-report-'.now()->format('Y-m-d-H-i-s').'.pdf';

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

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
    }

    private function getOverviewMetrics()
    {
        $now = Carbon::now();
        $lastMonth = $now->copy()->subMonth();

        return [
            'totalResidents' => Resident::count(),
            'totalHouseholds' => Household::count(),
            'totalDocumentRequests' => DocumentRequest::count(),
            'totalBlotters' => Blotter::count(),
            'totalCases' => CaseFile::count(),
            'totalPets' => Pet::count(),
            'totalUsers' => User::count(),
            'residentGrowth' => $this->getGrowthPercentage(Resident::class, 'last_month', $lastMonth),
            'documentRequestGrowth' => $this->getGrowthPercentage(DocumentRequest::class, 'last_month', $lastMonth),
            'blotterGrowth' => $this->getGrowthPercentage(Blotter::class, 'last_month', $lastMonth),
            'recentActivities' => $this->getRecentActivities(),
        ];
    }

    private function getGrowthPercentage(string $model, string $period, Carbon $compareDate): int
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

        return round((($currentCount - $previousCount) / $previousCount) * 100);
    }

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

    private function getResidentAnalytics(string $dateFrom, string $dateTo)
    {
        $residents = Resident::whereBetween('created_at', [$dateFrom, $dateTo])->get();

        // Gender distribution
        $genderStats = Resident::select('sex', DB::raw('COUNT(*) as count'))
            ->groupBy('sex')
            ->pluck('count', 'sex')
            ->toArray();

        // Age groups
        $ageGroups = [
            '0-17' => Resident::whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 0 AND 17')->count(),
            '18-30' => Resident::whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 18 AND 30')->count(),
            '31-50' => Resident::whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 31 AND 50')->count(),
            '51-65' => Resident::whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) BETWEEN 51 AND 65')->count(),
            '65+' => Resident::whereRaw('TIMESTAMPDIFF(YEAR, birth_date, CURDATE()) >= 65')->count(),
        ];

        // Civil status
        $civilStatusStats = Resident::select('civil_status', DB::raw('COUNT(*) as count'))
            ->groupBy('civil_status')
            ->pluck('count', 'civil_status')
            ->toArray();

        // Occupation
        $occupationStats = Resident::select('occupation', DB::raw('COUNT(*) as count'))
            ->groupBy('occupation')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'occupation')
            ->toArray();

        // Status distribution (active/inactive)
        $statusStats = Resident::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Inactive/Archived count (archived_at is not null)
        $inactiveCount = Resident::whereNotNull('archived_at')->count();

        // Monthly trends
        $residentMonthlyTrends = Resident::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->whereYear('created_at', '=', Carbon::now()->year)
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        return compact('genderStats', 'ageGroups', 'civilStatusStats', 'occupationStats', 'statusStats', 'inactiveCount', 'residentMonthlyTrends');
    }

    private function getHouseholdAnalytics()
    {
        // Total households
        $totalHouseholds = Household::count();

        // Average members per household - manually count from household_members table
        $totalMembers = DB::table('household_members')->count();
        $avgMembers = $totalHouseholds > 0 ? round($totalMembers / $totalHouseholds, 1) : 0;

        // Households by street
        $streetStats = Household::select('street', DB::raw('COUNT(*) as count'))
            ->groupBy('street')
            ->orderByDesc('count')
            ->pluck('count', 'street')
            ->toArray();

        // Households by phase
        $phaseStats = Household::select('phase', DB::raw('COUNT(*) as count'))
            ->groupBy('phase')
            ->orderByDesc('count')
            ->pluck('count', 'phase')
            ->toArray();

        // Households by household type
        $householdTypeStats = Household::select('household_type', DB::raw('COUNT(*) as count'))
            ->groupBy('household_type')
            ->pluck('count', 'household_type')
            ->toArray();

        // Households by homeownership type
        $homeownershipStats = Household::select('homeownership_type', DB::raw('COUNT(*) as count'))
            ->groupBy('homeownership_type')
            ->pluck('count', 'homeownership_type')
            ->toArray();

        // Socio-economic: Income range distribution
        $incomeRangeStats = Household::select('monthly_income_range', DB::raw('COUNT(*) as count'))
            ->groupBy('monthly_income_range')
            ->pluck('count', 'monthly_income_range')
            ->toArray();

        // Employment status distribution
        $employmentStats = Household::select('employment_status', DB::raw('COUNT(*) as count'))
            ->groupBy('employment_status')
            ->pluck('count', 'employment_status')
            ->toArray();

        // 4Ps beneficiaries
        $fourPsCount = Household::where('is_4ps_beneficiary', true)->count();
        $indigentCount = Household::where('is_indigent', true)->count();

        // Households with special members
        $pregnantCount = Household::where('has_pregnant_member', true)->count();
        $seniorCount = Household::where('has_senior_citizen', true)->count();
        $pwdCount = Household::where('has_pwd', true)->count();
        $chronicCount = Household::where('has_chronic_illness', true)->count();

        // Housing types
        $houseTypeStats = Household::select('house_type', DB::raw('COUNT(*) as count'))
            ->groupBy('house_type')
            ->pluck('count', 'house_type')
            ->toArray();

        // Utilities access
        $electricityAccess = Household::where('has_electricity', true)->count();
        $toiletAccess = Household::where('has_toilet', true)->count();
        $bathroomAccess = Household::where('has_bathroom', true)->count();
        $kitchenAccess = Household::where('has_kitchen', true)->count();
        $garageAccess = Household::where('has_garage', true)->count();

        // Disaster risk level
        $disasterRiskStats = Household::select('disaster_risk_level', DB::raw('COUNT(*) as count'))
            ->groupBy('disaster_risk_level')
            ->pluck('count', 'disaster_risk_level')
            ->toArray();

        // Monthly trends
        $householdMonthlyTrends = Household::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->whereYear('created_at', '=', Carbon::now()->year)
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
            'employmentStats',
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

    private function getDocumentRequestAnalytics(string $dateFrom, string $dateTo)
    {
        // By status
        $docStatusStats = DocumentRequest::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // By document type
        $typeStats = DocumentRequest::select('document_type_id', DB::raw('COUNT(*) as count'))
            ->groupBy('document_type_id')
            ->with('documentType')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->documentType->name ?? 'Unknown' => $item->count];
            })
            ->toArray();

        // Monthly trends
        $docMonthlyTrends = DocumentRequest::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->whereYear('created_at', '=', Carbon::now()->year)
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Processing time (average days from request to now/resolved)
        $avgProcessingDays = DocumentRequest::selectRaw('AVG(TIMESTAMPDIFF(DAY, created_at, NOW())) as avg_days')
            ->value('avg_days') ?? 0;

        // Approval rate
        $totalProcessed = DocumentRequest::whereIn('status', ['approved', 'released'])->count();
        $totalRequests = DocumentRequest::count();
        $approvalRate = $totalRequests > 0 ? round(($totalProcessed / $totalRequests) * 100) : 0;

        // Top requested types
        $topTypes = DocumentType::withCount('requests')
            ->orderByDesc('requests_count')
            ->limit(5)
            ->pluck('requests_count', 'name')
            ->toArray();

        return compact('docStatusStats', 'docMonthlyTrends', 'avgProcessingDays', 'approvalRate', 'topTypes');
    }

    private function getBlotterAnalytics(string $dateFrom, string $dateTo)
    {
        // By incident type
        $incidentTypeStats = Blotter::select('incident_type', DB::raw('COUNT(*) as count'))
            ->groupBy('incident_type')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'incident_type')
            ->toArray();

        // Monthly trends
        $blotterMonthlyTrends = Blotter::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->whereYear('created_at', '=', Carbon::now()->year)
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Resolution rate
        $resolved = Blotter::where('status', 'resolved')->count();
        $total = Blotter::count();
        $blotterResolutionRate = $total > 0 ? round(($resolved / $total) * 100) : 0;

        // Average resolution time
        $avgResolutionDays = Blotter::selectRaw('AVG(TIMESTAMPDIFF(DAY, created_at, NOW())) as avg_days')
            ->value('avg_days') ?? 0;

        // Case conversion rate (blotters that become cases)
        $total = Blotter::count();
        $withCase = Blotter::has('case')->count();
        $withoutCase = $total - $withCase;
        $caseConversionRate = $total > 0 ? round(($withCase / $total) * 100) : 0;

        return compact('incidentTypeStats', 'blotterMonthlyTrends', 'blotterResolutionRate', 'avgResolutionDays', 'caseConversionRate', 'withCase', 'withoutCase');
    }

    private function getCaseAnalytics(string $dateFrom, string $dateTo)
    {
        // By status
        $caseStatusStats = CaseFile::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // By case type (from blotter's incident_type)
        $caseTypeStats = CaseFile::join('blotters', 'cases.blotter_id', '=', 'blotters.id')
            ->select('blotters.incident_type', DB::raw('COUNT(*) as count'))
            ->groupBy('blotters.incident_type')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'blotters.incident_type')
            ->toArray();

        // Monthly trends
        $caseMonthlyTrends = CaseFile::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->whereYear('created_at', '=', Carbon::now()->year)
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // Resolution rate
        $resolved = CaseFile::where('status', 'closed')->count();
        $total = CaseFile::count();
        $resolutionRate = $total > 0 ? round(($resolved / $total) * 100) : 0;

        // Case aging (average days open)
        $avgDaysOpen = CaseFile::whereNull('closed_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(DAY, created_at, CURDATE())) as avg_days')
            ->value('avg_days') ?? 0;

        return compact('caseStatusStats', 'caseTypeStats', 'caseMonthlyTrends', 'resolutionRate', 'avgDaysOpen');
    }

    private function getPetAnalytics()
    {
        // By vaccination status (no status column, use vaccination_status)
        $petVaxStats = Pet::select('vaccination_status', DB::raw('COUNT(*) as count'))
            ->groupBy('vaccination_status')
            ->pluck('count', 'vaccination_status')
            ->toArray();

        // By species
        $typeStats = Pet::select('species', DB::raw('COUNT(*) as count'))
            ->groupBy('species')
            ->pluck('count', 'species')
            ->toArray();

        // By breed
        $breedStats = Pet::select('breed', DB::raw('COUNT(*) as count'))
            ->groupBy('breed')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'breed')
            ->toArray();

        // Vaccination status (matching form values: up-to-date, overdue, not-vaccinated)
        $upToDate = Pet::where('vaccination_status', 'up-to-date')->count();
        $overdue = Pet::where('vaccination_status', 'overdue')->count();
        $notVaccinated = Pet::where('vaccination_status', 'not-vaccinated')->count();

        $vaccinationStats = [
            'up-to-date' => $upToDate,
            'overdue' => $overdue,
            'not-vaccinated' => $notVaccinated,
        ];

        // For the 2-card stat display: Vaccinated = up-to-date, Not Vaccinated = overdue + not-vaccinated
        $vaccinatedCount = $upToDate;
        $notVaccinatedCount = $overdue + $notVaccinated;

        // Monthly trends
        $petMonthlyTrends = Pet::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as count')
        )
            ->whereYear('created_at', '=', Carbon::now()->year)
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        return compact('typeStats', 'vaccinationStats', 'petMonthlyTrends');
    }
}
