<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Report - Barangay Bucandala 1</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.5;
            padding: 25px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #1055C9;
        }
        .header h1 {
            font-size: 24px;
            color: #1055C9;
            margin-bottom: 8px;
        }
        .header h2 {
            font-size: 18px;
            color: #444;
            font-weight: 400;
        }
        .meta {
            font-size: 11px;
            color: #666;
            margin-bottom: 25px;
            padding: 12px;
            background: #f5f5f5;
            border-radius: 6px;
        }
        .section {
            page-break-inside: avoid;
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 15px;
            font-weight: 700;
            color: #1055C9;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e5e7eb;
        }
        .summary-text {
            font-size: 10px;
            color: #555;
            line-height: 1.6;
            margin-bottom: 15px;
            padding: 10px;
            background: #f8fafc;
            border-left: 3px solid #1055C9;
            border-radius: 0 6px 6px 0;
        }
        .stats-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }
        .stat-box {
            flex: 1 1 130px;
            min-width: 100px;
            padding: 15px 12px;
            background: #fff;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #e5e7eb;
        }
        .stat-box .value {
            font-size: 22px;
            font-weight: 700;
            color: #1f2937;
        }
        .stat-box .label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 4px;
        }
        .stat-box .growth {
            font-size: 10px;
            margin-top: 5px;
            font-weight: 600;
        }
        .stat-box .growth.positive { color: #16a34a; }
        .stat-box .growth.negative { color: #dc2626; }
        .charts-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
            margin: 20px 0;
            justify-content: center;
        }
        .chart-container {
            text-align: center;
            padding: 15px;
            background: #fff;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        .chart-container h4 {
            font-size: 11px;
            color: #444;
            margin-bottom: 10px;
            font-weight: 600;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            font-size: 10px;
        }
        .table th {
            background: #f3f4f6;
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
            border-bottom: 1px solid #d1d5db;
        }
        .table td {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        .table tr:nth-child(even) { background: #f9fafb; }
        .two-col {
            display: flex;
            gap: 20px;
        }
        .two-col > div {
            flex: 1;
        }
        .highlight-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }
        .highlight-box h4 {
            color: #1e40af;
            font-size: 12px;
            margin-bottom: 10px;
        }
        .highlight-box ul {
            margin: 0;
            padding-left: 20px;
            font-size: 10px;
            color: #444;
        }
        .highlight-box li {
            margin-bottom: 5px;
        }
        .page-break { page-break-after: always; }
        .footer {
            position: fixed;
            bottom: 10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Barangay Bucandala 1</h1>
        <h2>Comprehensive Analytics Report</h2>
    </div>
    
    <div class="meta">
        <strong>Report Period:</strong> {{ $dateFrom }} to {{ $dateTo }} &nbsp;|&nbsp;
        <strong>Generated:</strong> {{ now()->format('Y-m-d H:i:s') }}
    </div>

    <div class="section">
        <div class="section-title">Executive Summary</div>
        <div class="summary-text">
            This report provides a comprehensive overview of barangay operations including resident demographics, 
            document processing, blotter records, case management, and pet registry. The data covers the specified 
            period and includes comparative analysis where applicable.
        </div>
        <div class="stats-grid">
            <div class="stat-box">
                <div class="value">{{ number_format($overview['totalResidents'] ?? 0) }}</div>
                <div class="label">Total Residents</div>
                @if(isset($overview['residentGrowth']))
                <div class="growth {{ $overview['residentGrowth'] >= 0 ? 'positive' : 'negative' }}">
                    {{ $overview['residentGrowth'] >= 0 ? '+' : '' }}{{ $overview['residentGrowth'] }}% vs last month
                </div>
                @endif
            </div>
            <div class="stat-box">
                <div class="value">{{ number_format($overview['totalHouseholds'] ?? 0) }}</div>
                <div class="label">Households</div>
            </div>
            <div class="stat-box">
                <div class="value">{{ number_format($overview['totalDocumentRequests'] ?? 0) }}</div>
                <div class="label">Document Requests</div>
                @if(isset($overview['documentRequestGrowth']))
                <div class="growth {{ $overview['documentRequestGrowth'] >= 0 ? 'positive' : 'negative' }}">
                    {{ $overview['documentRequestGrowth'] >= 0 ? '+' : '' }}{{ $overview['documentRequestGrowth'] }}% vs last month
                </div>
                @endif
            </div>
            <div class="stat-box">
                <div class="value">{{ number_format($overview['totalBlotters'] ?? 0) }}</div>
                <div class="label">Blotter Cases</div>
                @if(isset($overview['blotterGrowth']))
                <div class="growth {{ $overview['blotterGrowth'] >= 0 ? 'positive' : 'negative' }}">
                    {{ $overview['blotterGrowth'] >= 0 ? '+' : '' }}{{ $overview['blotterGrowth'] }}% vs last month
                </div>
                @endif
            </div>
            <div class="stat-box">
                <div class="value">{{ number_format($overview['totalCases'] ?? 0) }}</div>
                <div class="label">Case Files</div>
            </div>
            <div class="stat-box">
                <div class="value">{{ number_format($overview['totalPets'] ?? 0) }}</div>
                <div class="label">Registered Pets</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Resident Demographics Analysis</div>
        
        @php
        $totalResidents = $overview['totalResidents'] ?? 0;
        $maleCount = $residents['genderStats']['Male'] ?? 0;
        $femaleCount = $residents['genderStats']['Female'] ?? 0;
        $malePct = $totalResidents > 0 ? round(($maleCount / $totalResidents) * 100) : 0;
        $femalePct = $totalResidents > 0 ? round(($femaleCount / $totalResidents) * 100) : 0;
        @endphp
        
        <div class="summary-text">
            The barangay has <strong>{{ number_format($totalResidents) }} registered residents</strong>. 
            Gender distribution shows <strong>{{ number_format($maleCount) }} males ({{ $malePct }}%)</strong> and 
            <strong>{{ number_format($femaleCount) }} females ({{ $femalePct }}%)</strong>. 
            The age groups and civil status information provide deeper insights into the population structure.
        </div>
        
        <div class="charts-grid">
            <div class="chart-container">
                <h4>Gender Distribution</h4>
                {!! $charts['genderPie'] ?? '' !!}
            </div>
            <div class="chart-container">
                <h4>Age Distribution</h4>
                {!! $charts['ageBar'] ?? '' !!}
            </div>
        </div>
        
        <div class="two-col">
            <div>
                <h4 style="font-size:11px;color:#444;margin-bottom:8px;">Age Group Breakdown</h4>
                <table class="table">
                    <thead><tr><th>Age Group</th><th>Count</th></tr></thead>
                    <tbody>
                        @foreach($residents['ageGroups'] ?? [] as $age => $count)
                        <tr><td>{{ $age }} years</td><td>{{ number_format($count) }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div>
                <h4 style="font-size:11px;color:#444;margin-bottom:8px;">Civil Status Distribution</h4>
                <table class="table">
                    <thead><tr><th>Status</th><th>Count</th></tr></thead>
                    <tbody>
                        @foreach($residents['civilStatusStats'] ?? [] as $status => $count)
                        <tr><td>{{ ucfirst($status) }}</td><td>{{ number_format($count) }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        @if(!empty($residents['occupationStats']))
        <div style="margin-top:15px;">
            <h4 style="font-size:11px;color:#444;margin-bottom:8px;">Top Occupations</h4>
            <table class="table">
                <thead><tr><th>Occupation</th><th>Count</th></tr></thead>
                <tbody>
                    @foreach(array_slice($residents['occupationStats'], 0, 8) as $occ => $count)
                    <tr><td>{{ $occ }}</td><td>{{ number_format($count) }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <div class="page-break"></div>

    <div class="section">
        <div class="section-title">Document Request Analysis</div>
        
        @php
        $docTotal = $overview['totalDocumentRequests'] ?? 0;
        $approvalRate = $documents['approvalRate'] ?? 0;
        $avgDays = $documents['avgProcessingDays'] ?? 0;
        $topTypes = $documents['topTypes'] ?? [];
        @endphp
        
        <div class="summary-text">
            A total of <strong>{{ number_format($docTotal) }} document requests</strong> have been processed. 
            The <strong>{{ $approvalRate }}% approval rate</strong> reflects the efficiency of the document processing system. 
            On average, documents are processed within <strong>{{ number_format($avgDays, 1) }} days</strong>. 
            Common document types include {{ implode(', ', array_keys($topTypes)) }}.
        </div>
        
        <div class="charts-grid">
            <div class="chart-container">
                <h4>Top Document Types Requested</h4>
                {!! $charts['docTypePie'] ?? '' !!}
            </div>
        </div>
        
        <div class="highlight-box">
            <h4>Document Processing Insights</h4>
            <ul>
                <li>Approval Rate: {{ $approvalRate }}% of all requests were approved</li>
                <li>Average Processing Time: {{ number_format($avgDays, 1) }} days per document</li>
                <li>Most Requested: {{ !empty($topTypes) ? array_keys($topTypes)[0] : 'N/A' }}</li>
            </ul>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Blotter Records Analysis</div>
        
        @php
        $totalBlotters = $overview['totalBlotters'] ?? 0;
        $resolutionRate = $blotters['blotterResolutionRate'] ?? 0;
        $caseConversion = $blotters['caseConversionRate'] ?? 0;
        $withCase = $blotters['withCase'] ?? 0;
        $withoutCase = $blotters['withoutCase'] ?? 0;
        $incidentStats = $blotters['incidentTypeStats'] ?? [];
        @endphp
        
        <div class="summary-text">
            The barangay has recorded <strong>{{ number_format($totalBlotters) }} blotter cases</strong>. 
            The <strong>{{ $resolutionRate }}% resolution rate</strong> indicates successful mediation in most incidents. 
            <strong>{{ $caseConversion }}%</strong> of blotter cases were elevated to formal case files for further legal action.
        </div>
        
        <div class="charts-grid">
            <div class="chart-container">
                <h4>Incident Types Distribution</h4>
                {!! $charts['incidentPie'] ?? '' !!}
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-box">
                <div class="value">{{ $resolutionRate }}%</div>
                <div class="label">Resolution Rate</div>
            </div>
            <div class="stat-box">
                <div class="value">{{ $caseConversion }}%</div>
                <div class="label">Case Conversion</div>
            </div>
            <div class="stat-box">
                <div class="value">{{ number_format($withCase) }}</div>
                <div class="label">With Case File</div>
            </div>
            <div class="stat-box">
                <div class="value">{{ number_format($withoutCase) }}</div>
                <div class="label">Standalone</div>
            </div>
        </div>
        
        @if(!empty($incidentStats))
        <div class="highlight-box">
            <h4>Top Incident Types</h4>
            <ul>
                @foreach(array_slice($incidentStats, 0, 5) as $type => $count)
                <li>{{ $type }}: {{ number_format($count) }} cases</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <div class="page-break"></div>

    <div class="section">
        <div class="section-title">Case Files Analysis</div>
        
        @php
        $totalCases = $overview['totalCases'] ?? 0;
        $caseResolutionRate = $cases['resolutionRate'] ?? 0;
        $avgDaysOpen = $cases['avgDaysOpen'] ?? 0;
        $caseTypeStats = $cases['caseTypeStats'] ?? [];
        @endphp
        
        <div class="summary-text">
            There are <strong>{{ number_format($totalCases) }} active case files</strong> in the barangay system. 
            The <strong>{{ $caseResolutionRate }}% resolution rate</strong> reflects closed cases. 
            Currently open cases have been pending for an average of <strong>{{ number_format($avgDaysOpen, 1) }} days</strong>.
        </div>
        
        <div class="stats-grid">
            <div class="stat-box">
                <div class="value">{{ $caseResolutionRate }}%</div>
                <div class="label">Resolution Rate</div>
            </div>
            <div class="stat-box">
                <div class="value">{{ number_format($avgDaysOpen, 1) }}</div>
                <div class="label">Avg. Days Open</div>
            </div>
        </div>
        
        @if(!empty($caseTypeStats))
        <div class="highlight-box">
            <h4>Case Types</h4>
            <ul>
                @foreach(array_slice($caseTypeStats, 0, 5) as $type => $count)
                <li>{{ $type }}: {{ number_format($count) }} cases</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Pet Registry Analysis</div>
        
        @php
        $totalPets = $overview['totalPets'] ?? 0;
        $vaxStats = $pets['vaccinationStats'] ?? [];
        $upToDate = $vaxStats['up-to-date'] ?? 0;
        $overdue = $vaxStats['overdue'] ?? 0;
        $notVax = $vaxStats['not-vaccinated'] ?? 0;
        $vaxRate = $totalPets > 0 ? round(($upToDate / $totalPets) * 100) : 0;
        @endphp
        
        <div class="summary-text">
            The barangay has <strong>{{ number_format($totalPets) }} registered pets</strong>. 
            Vaccination status shows <strong>{{ number_format($upToDate) }} up-to-date ({{ $vaxRate }}%)</strong>, 
            <strong>{{ number_format($overdue) }} overdue</strong>, and 
            <strong>{{ number_format($notVax) }} not vaccinated</strong>. 
            Regular vaccination drives are conducted to maintain public health and safety.
        </div>
        
        <div class="charts-grid">
            <div class="chart-container">
                <h4>Pet Types</h4>
                {!! $charts['petTypePie'] ?? '' !!}
            </div>
            <div class="chart-container">
                <h4>Vaccination Status</h4>
                {!! $charts['vaxPie'] ?? '' !!}
            </div>
        </div>
        
        <div class="highlight-box">
            <h4>Pet Registry Summary</h4>
            <ul>
                <li>Total Registered Pets: {{ number_format($totalPets) }}</li>
                <li>Up-to-date Vaccination: {{ number_format($upToDate) }} ({{ $vaxRate }}%)</li>
                <li>Overdue Vaccination: {{ number_format($overdue) }}</li>
                <li>Not Vaccinated: {{ number_format($notVax) }}</li>
            </ul>
        </div>
    </div>

    <div class="footer">
        Barangay Bucandala 1 Analytics Report - Generated {{ now()->format('F d, Y') }}
    </div>
</body>
</html>