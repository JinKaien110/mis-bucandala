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

        .chart-label {
            font-size: 11px;
            color: #444;
            margin-bottom: 10px;
            font-weight: 600;
            padding: 15px;
            background: #fff;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .charts-grid {

            display: grid;
            grid-template-columns: 1fr 1fr; /* strict 2 columns */
            gap: 18px 18px;
            margin: 20px 0;
            align-items: start;
        }

        .chart-container {
            text-align: center;
            padding: 15px;
            background: #fff;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            width: 100%; /* grid item */
            box-sizing: border-box;
        }

        /* Legacy h4 styles kept; labels are rendered via .chart-label */
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

    @if(!empty($overview) && array_key_exists('totalResidents', $overview))
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
                    <div class="growth {{ ($overview['residentGrowth'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
                        {{ ($overview['residentGrowth'] ?? 0) >= 0 ? '+' : '' }}{{ $overview['residentGrowth'] }}% vs last month
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
                    <div class="growth {{ ($overview['documentRequestGrowth'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
                        {{ ($overview['documentRequestGrowth'] ?? 0) >= 0 ? '+' : '' }}{{ $overview['documentRequestGrowth'] }}% vs last month
                    </div>
                @endif
            </div>

            <div class="stat-box">
                <div class="value">{{ number_format($overview['totalBlotters'] ?? 0) }}</div>
                <div class="label">Blotter Cases</div>
                @if(isset($overview['blotterGrowth']))
                    <div class="growth {{ ($overview['blotterGrowth'] ?? 0) >= 0 ? 'positive' : 'negative' }}">
                        {{ ($overview['blotterGrowth'] ?? 0) >= 0 ? '+' : '' }}{{ $overview['blotterGrowth'] }}% vs last month
                    </div>
                @endif
            </div>

            <div class="stat-box">
                <div class="value">{{ number_format($overview['totalCases'] ?? 0) }}</div>
                <div class="label">Case Files</div>
            </div>

            {{-- Newly added executive cards (from dashboard) --}}
            <div class="stat-box">
                <div class="value">{{ 'PHP ' . number_format($overview['paymentAnalytics']['totalCollections'] ?? 0) }}</div>
                <div class="label">Payment</div>
            </div>

            <div class="stat-box">
                <div class="value">{{ number_format($overview['paymentAnalytics']['paidTransactions'] ?? 0) }}</div>
                <div class="label">Paid Transactions</div>
            </div>
        </div>
    </div>

    @endif

    @php
        // Strong gating: only render this section when Residents section is actually selected.
        // AnalyticsService should omit Residents payload when Residents section is unchecked.
        $sectionSelectedResidents = !empty($residents)
            && isset($residents['genderStats']['Male'], $residents['genderStats']['Female'])
            && isset($residents['ageGroups']);

    @endphp


    @if($sectionSelectedResidents)
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
            @if(isset($charts['genderPie']) && !empty($charts['genderPie']))
                <div class="chart-container">
                    <div class="chart-label">Gender Distribution</div>
                    <img src="data:image/svg+xml;base64,{{ base64_encode($charts['genderPie']) }}">
                </div>
            @else
                <div class="chart-container">
                    <div class="chart-label">Gender Distribution</div>
                    <div class="summary-text">Chart SVG missing (genderPie). length={{ isset($charts['genderPie']) && is_string($charts['genderPie']) ? strlen($charts['genderPie']) : 0 }}</div>
                </div>
            @endif

            @if(isset($charts['ageBar']) && !empty($charts['ageBar']))
                <div class="chart-container">
                    <div class="chart-label">Age Distribution</div>
                    <img src="data:image/svg+xml;base64,{{ base64_encode($charts['ageBar']) }}">
                </div>
            @else
                <div class="chart-container">
                    <div class="chart-label">Age Distribution</div>
                    <div class="summary-text">Chart SVG missing (ageBar). length={{ isset($charts['ageBar']) && is_string($charts['ageBar']) ? strlen($charts['ageBar']) : 0 }}</div>
                </div>
            @endif

            @if(isset($charts['civilStatusPie']) && !empty($charts['civilStatusPie']))
                <div class="chart-container">
                    <div class="chart-label">Civil Status Distribution</div>
                    <img src="data:image/svg+xml;base64,{{ base64_encode($charts['civilStatusPie']) }}">
                </div>
            @else
                <div class="chart-container">
                    <div class="chart-label">Civil Status Distribution</div>
                    <div class="summary-text">Chart SVG missing (civilStatusPie). length={{ isset($charts['civilStatusPie']) && is_string($charts['civilStatusPie']) ? strlen($charts['civilStatusPie']) : 0 }}</div>
                </div>
            @endif

            @if(isset($charts['classificationBar']) && !empty($charts['classificationBar']))
                <div class="chart-container">
                    <div class="chart-label">Resident Classifications</div>
                    <img src="data:image/svg+xml;base64,{{ base64_encode($charts['classificationBar']) }}">
                </div>
            @else
                <div class="chart-container">
                    <div class="chart-label">Resident Classifications</div>
                    <div class="summary-text">Chart SVG missing (classificationBar). length={{ isset($charts['classificationBar']) && is_string($charts['classificationBar']) ? strlen($charts['classificationBar']) : 0 }}</div>
                </div>
            @endif

            @if(isset($charts['phasePie']) && !empty($charts['phasePie']))
                <div class="chart-container">
                    <div class="chart-label">Population by Phase</div>
                    <img src="data:image/svg+xml;base64,{{ base64_encode($charts['phasePie']) }}">
                </div>
            @else
                <div class="chart-container">
                        <div class="chart-label">Population by Phase</div>
                    <div class="summary-text">Chart SVG missing (phasePie). length={{ isset($charts['phasePie']) && is_string($charts['phasePie']) ? strlen($charts['phasePie']) : 0 }}</div>
                </div>
            @endif
        </div>

        @if(isset($residents['ageGroups']))
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

                    {{-- New: classifications chart-equivalent table (PWD / Solo Parent / Indigent / 4Ps) --}}
                    <div style="margin-top:14px;">
                        <h4 style="font-size:11px;color:#444;margin-bottom:8px;">Resident Classifications</h4>
                        @php
                            $classificationRows = [
                                'PWD' => (int)($residents['pwdResidents'] ?? 0),
                                'Solo Parents' => (int)($residents['soloParentResidents'] ?? 0),
                                'Indigent' => (int)($residents['indigentResidents'] ?? 0),
                                '4Ps Beneficiaries' => (int)($residents['fourPsBeneficiaries'] ?? 0),
                            ];
                        @endphp
                        <table class="table">
                            <thead><tr><th>Classification</th><th>Count</th></tr></thead>
                            <tbody>
                                @foreach($classificationRows as $label => $count)
                                    <tr>
                                        <td>{{ $label }}</td>
                                        <td>{{ number_format($count) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @php
                        $civilStatusOrder = ['single', 'married', 'widowed', 'separated', 'divorced'];
                        $civilStats = $residents['civilStatusStats'] ?? [];
                    @endphp

                    <table class="table">
                        <thead><tr><th>Status</th><th>Count</th></tr></thead>
                        <tbody>
                            @php
                                $civilStatsExtraKeys = array_diff(array_keys($civilStats), $civilStatusOrder);
                            @endphp

                            @foreach($civilStatusOrder as $statusKey)
                                <tr>
                                    <td>{{ ucfirst($statusKey) }}</td>
                                    <td>{{ number_format((int)($civilStats[$statusKey] ?? 0)) }}</td>
                                </tr>
                            @endforeach

                            @foreach($civilStatsExtraKeys as $statusKey)
                                <tr>
                                    <td>{{ ucfirst($statusKey) }}</td>
                                    <td>{{ number_format((int)($civilStats[$statusKey] ?? 0)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Residents list table (PDF) - only for pdf_charts_table_lists / Residents selected.
             Expects $residentsList as an array of row objects/arrays.
        --}}
        @if(!empty($includeRecordTables) && !empty($residentsList))
            <div style="margin-top:15px;">
                <h4 style="font-size:11px;color:#444;margin-bottom:8px;">Residents List</h4>
                <table class="table" style="font-size:9px;">
                    <thead class="bg-light text-uppercase small text-muted">
                        <tr>
                            <th>#</th>
                            <th>Account Number </th>
        <th>Resident</th>
        <th>Address</th>
        <th>Contact</th>
        <th>Status</th>
        <th>Occupation</th>
        <th>Monthly Income</th>
        <th>Civil Status</th>
        <th>Educational Attainment</th>
        <th>4Ps Beneficiary</th>
        <th>PWD</th>
        <th>Solo Parent</th>
        <th>Indigent</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($residentsList as $resident)
    <tr>
    <td>{{ $loop->iteration }}</td>
        <td>{{ $resident['account_no'] }}</td>

        <td>
            {{
                trim(
                    ($resident['last_name'] ?? '') . ', ' .
                    ($resident['first_name'] ?? '') . ' ' .
                    ($resident['middle_name'] ?? '') . ' ' .
                    ($resident['suffix'] ?? '')
                )
            }}
        </td>

  <td>
    {{ $resident['address_line'] ?? '-' }}

    @if(!empty($resident['phase']))
        - Phase {{ $resident['phase'] }}
    @else
        - No Phase
    @endif
</td>

        <td>{{ $resident['contact_no'] ?? '-' }}</td>

        <td>{{ ucfirst($resident['status'] ?? '-') }}</td>

        <td>{{ $resident['occupation'] ?? '-' }}</td>

        <td>{{ $resident['monthly_income'] ?? '-' }}</td>

        <td>{{ $resident['civil_status'] ?? '-' }}</td>

        <td>{{ $resident['educational_attainment'] ?? '-' }}</td>

        <td>{{ !empty($resident['four_ps_beneficiary']) ? 'Yes' : 'No' }}</td>

        <td>{{ !empty($resident['pwd']) ? 'Yes' : 'No' }}</td>

        <td>{{ !empty($resident['solo_parent']) ? 'Yes' : 'No' }}</td>

        <td>{{ !empty($resident['indigent']) ? 'Yes' : 'No' }}</td>

    </tr>
@endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if(isset($residents['occupationStats']))
            <div style="margin-top:15px;">
                <h4 style="font-size:11px;color:#444;margin-bottom:8px;">Top Occupations</h4>
                <table class="table">

                    <thead><tr><th>Occupation</th><th>Count</th></tr></thead>
                    <tbody>
                        @php
                            $occupationOrder = [
                                'student' => 'Student',
                                'employed' => 'Employed (Regular)',
                                'contractual' => 'Contractual/Temporary',
                                'self_employed' => 'Self-employed',
                                'business_owner' => 'Business Owner',
                                'farmer' => 'Farmer',
                                'fisherman' => 'Fisherman',
                                'vendor' => 'Vendor/Trader',
                                'artisan' => 'Artisan/Craftsman',
                                'driver' => 'Driver',
                                'construction_worker' => 'Construction Worker',
                                'laborer' => 'General Laborer',
                                'domestic_worker' => 'Domestic Worker',
                                'healthcare' => 'Healthcare Worker',
                                'teacher' => 'Teacher',
                                'engineer' => 'Engineer',
                                'accountant' => 'Accountant',
                                'manager' => 'Manager/Supervisor',
                                'administrative' => 'Administrative/Clerical',
                                'sales' => 'Sales Representative',
                                'service' => 'Service Industry',
                                'hospitality' => 'Hospitality/Tourism',
                                'transport' => 'Transportation',
                                'manufacturing' => 'Manufacturing',
                                'it_professional' => 'IT Professional',
                                'creative' => 'Creative Professional',
                                'ofw' => 'Overseas Filipino Worker (OFW)',
                                'retired' => 'Retired',
                                'unemployed' => 'Unemployed',
                                'pwswd' => 'PWD/Senior Citizen (Unable to Work)',
                                'homemaker' => 'Homemaker',
                                'other' => 'Other'
                            ];
                            $occupationStats = $residents['occupationStats'] ?? [];
                        @endphp

                        @php
                            $occupationStatsExtraKeys = array_diff(array_keys($occupationStats), array_keys($occupationOrder));
                        @endphp

                        @foreach($occupationOrder as $occKey => $occLabel)
                            <tr>
                                <td>{{ $occLabel }}</td>
                                <td>{{ number_format((int)($occupationStats[$occKey] ?? 0)) }}</td>
                            </tr>
                        @endforeach

                        @foreach($occupationStatsExtraKeys as $occKey)
                            <tr>
                                <td>{{ is_string($occKey) ? $occKey : (string)$occKey }}</td>
                                <td>{{ number_format((int)($occupationStats[$occKey] ?? 0)) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>



    @endif

    
    @if(isset($households['totalHouseholds']))

        <div class="page-break"></div>

        <div class="section">
            <div class="section-title">Household Analytics</div>

            @if(!empty($includeRecordTables))
                {{-- TODO: Underlying record tables appended in next implementation step.
                     This placeholder will be replaced with real tables in a follow-up commit. --}}
            @endif

            <div class="summary-text">
                Household data covers total households and key socio-economic indicators for the selected period.
            </div>

            @php
                $householdClassificationRows = [
                    'PWD' => (int) ($households['pwdCount'] ?? 0),
                    'Senior Citizens' => (int) ($households['seniorCount'] ?? 0),
                    'Pregnant Members' => (int) ($households['pregnantCount'] ?? 0),
                    'Chronic Illness' => (int) ($households['chronicCount'] ?? 0),
                ];
            @endphp

            <div class="two-col">
                <div>
                    <h4 style="font-size:11px;color:#444;margin-bottom:8px;">Household Member Flags (Counts)</h4>
                    <table class="table">
                        <thead><tr><th>Flag</th><th>Household Count</th></tr></thead>
                        <tbody>
                            @foreach($householdClassificationRows as $label => $count)
                                <tr>
                                    <td>{{ $label }}</td>
                                    <td>{{ number_format($count) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div>
                    <h4 style="font-size:11px;color:#444;margin-bottom:8px;">Housing & Socio-Economic Snapshot</h4>
                    <table class="table">
                        <thead><tr><th>Metric</th><th>Value</th></tr></thead>
                        <tbody>
                            <tr><td>Total Households</td><td>{{ number_format((int)($households['totalHouseholds'] ?? 0)) }}</td></tr>
                            <tr><td>4Ps Beneficiary Households</td><td>{{ number_format((int)($households['fourPsCount'] ?? 0)) }}</td></tr>
                            <tr><td>Average Members / Household</td><td>{{ number_format((float)($households['avgMembers'] ?? 0), 1) }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="charts-grid">
                @if(isset($charts['homeownershipBar']) && !empty($charts['homeownershipBar']))
                    <div class="chart-container">
                        <div class="chart-label">Homeownership Distribution</div>
                        <img src="data:image/svg+xml;base64,{{ base64_encode($charts['homeownershipBar']) }}">
                    </div>
                @else
                    <div class="chart-container">
                        <div class="chart-label">Homeownership Distribution</div>
                        <div class="summary-text">Chart SVG missing (homeownershipBar). length={{ isset($charts['homeownershipBar']) && is_string($charts['homeownershipBar']) ? strlen($charts['homeownershipBar']) : 0 }}</div>
                    </div>
                @endif

                @if(isset($charts['householdPhasePie']) && !empty($charts['householdPhasePie']))
                    <div class="chart-container">
                        <div class="chart-label">Households by Phase</div>
                        <img src="data:image/svg+xml;base64,{{ base64_encode($charts['householdPhasePie']) }}">
                    </div>
                @else
                    <div class="chart-container">
                    <div class="chart-label">Households by Phase</div>
                        <div class="summary-text">Chart SVG missing (householdPhasePie). length={{ isset($charts['householdPhasePie']) && is_string($charts['householdPhasePie']) ? strlen($charts['householdPhasePie']) : 0 }}</div>
                    </div>
                @endif

                @if(isset($charts['incomeRangePie']) && !empty($charts['incomeRangePie']))
                    <div class="chart-container">
                        <div class="chart-label">Income Range Distribution</div>
                        <img src="data:image/svg+xml;base64,{{ base64_encode($charts['incomeRangePie']) }}">
                    </div>
                @endif






            </div>

   @if(!empty($includeRecordTables) && !empty($householdsList))

<div style="margin-top:15px;">
    <h4 style="font-size:11px;color:#444;margin-bottom:8px;">Household List</h4>

    <table class="table" style="font-size:9px;">

        <thead class="bg-light text-uppercase small text-muted">
            <tr>
                <th>#</th>
                <th>Household Code</th>
                <th>Head of Family</th>
                <th>Contact No.</th>
                <th>Phase</th>
                <th>Address</th>
                <th>Members</th>
            </tr>
        </thead>

        <tbody>

            @foreach($householdsList as $household)

                <tr>

                    <td>{{ $loop->iteration }}</td>

                    <td>{{ $household['household_code'] ?? '-' }}</td>

                    <td>{{ $household['head_fullname'] ?? '-' }}</td>

                    <td>{{ $household['head_contact'] ?? '-' }}</td>

                    <td>
                        {{
                            !empty($household['phase'])
                                ? 'Phase ' . $household['phase']
                                : 'No Phase'
                        }}
                    </td>

                    <td>{{ $household['address_line'] ?? '-' }}</td>

                    <td>{{ $household['members_count'] ?? 0 }}</td>

                </tr>

            @endforeach

        </tbody>

    </table>
</div>

@endif
        </div>


    @endif

    
    @if(isset($documents['totalDocumentRequests']))
        <div class="page-break"></div>


        <div class="section">
            <div class="section-title">Document Request Analysis</div>

            @php
                // Must NOT depend on Executive overview payload.
                $docTotal = $documents['totalDocumentRequests']
                    ?? $overview['totalDocumentRequests']
                    ?? 0;

                $approvalRate = $documents['approvalRate'] ?? 0;
                $topTypes = $documents['topTypes'] ?? [];
                $avgDays = $documents['avgDays']
                    ?? ($documents['avgProcessingDays'] ?? ($overview['avgDays'] ?? 0));

            @endphp

            <div class="summary-text">
                A total of <strong>{{ number_format($docTotal) }} document requests</strong> have been processed.
                The <strong>{{ $approvalRate }}% approval rate</strong> reflects the efficiency of the document processing system.
                On average, documents are processed within <strong>{{ number_format($avgDays, 1) }} days</strong>.
                Common document types include {{ implode(', ', array_keys($topTypes)) }}.
            </div>

            <div class="charts-grid">
                <div class="chart-container">
                    <div class="chart-label">Top Document Types Requested</div>
                    <img src="data:image/svg+xml;base64,{{ base64_encode($charts['docTypePie'] ?? '') }}">
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

        @if(!empty($includeRecordTables) && !empty($documentsList))

<div style="margin-top:15px;">
    <h4 style="font-size:11px;color:#444;margin-bottom:8px;">
        Document Request List
    </h4>

    <table class="table" style="font-size:9px;">

        <thead class="bg-light text-uppercase small text-muted">
            <tr>
                <th>#</th>
                <th>Control No.</th>
                <th>Resident</th>
                <th>Document</th>
                <th>Fee</th>
                <th>Requested At</th>
                <th>Reason for Request</th>
            </tr>
        </thead>

        <tbody>

            @foreach($documentsList as $document)

                <tr>

                    <td>{{ $loop->iteration }}</td>

                    <td>{{ $document['control_no'] ?? '-' }}</td>

                    <td>{{ $document['resident_name'] ?? '-' }}</td>

                    <td>{{ $document['document_name'] ?? '-' }}</td>

                    <td>
                        PHP {{ number_format((float)($document['fee_amount'] ?? 0), 2) }}
                    </td>

                    <td>{{ $document['requested_at'] ?? '-' }}</td>

                    <td>{{ $document['purpose'] ?? '-' }}</td>

                </tr>

            @endforeach

        </tbody>

    </table>
</div>

@endif
    @endif

    {{-- Payments --}}
    @php
        $paymentPayload = $overview['paymentAnalytics'] ?? ($paymentAnalytics['paymentAnalytics'] ?? $paymentAnalytics ?? []);
        $paymentPayload = is_array($paymentPayload) ? $paymentPayload : [];
    @endphp
    @if(!empty($paymentPayload) || !empty($charts['paymentStatusChart'] ?? null))


        <div class="page-break"></div>
        <div class="section">
            <div class="section-title">Payments Overview</div>
            <div class="summary-text">Payment totals and status distribution for the selected period.</div>

            <div class="stats-grid" style="margin-top:10px;">
                <div class="stat-box">
                    <div class="value">PHP {{ number_format($paymentPayload['totalCollections'] ?? 0) }}</div>
                    <div class="label">Total Collections</div>
                </div>
                <div class="stat-box">
                    <div class="value">{{ number_format($paymentPayload['paidTransactions'] ?? 0) }}</div>
                    <div class="label">Paid Transactions</div>
                </div>
                <div class="stat-box">
                    <div class="value">{{ number_format($paymentPayload['unpaidTransactions'] ?? 0) }}</div>
                    <div class="label">Unpaid Transactions</div>
                </div>
                <div class="stat-box">
                    <div class="value">{{ number_format($paymentPayload['totalTransactions'] ?? 0) }}</div>
                    <div class="label">Total Transactions</div>
                </div>
            </div>

            <div class="charts-grid" style="margin-top:10px;">
                <div class="chart-container">
                    <div class="chart-label">Payment Status Distribution</div>
                    @if(isset($charts['paymentStatusChart']) && !empty($charts['paymentStatusChart']))
                        <img src="data:image/svg+xml;base64,{{ base64_encode($charts['paymentStatusChart']) }}">
                    @else
                        <div class="summary-text">Chart not available (paymentStatusChart).</div>
                    @endif
                </div>
                <div class="chart-container">

                    <div class="chart-label">Monthly Revenue Trend</div>

                    @if(isset($charts['monthlyRevenueBar']) && !empty($charts['monthlyRevenueBar']))
                        <img src="data:image/svg+xml;base64,{{ base64_encode($charts['monthlyRevenueBar']) }}" alt="Monthly Revenue Trend" />
                    @else
                        <div class="summary-text">Chart not available (monthlyRevenueBar).</div>
                    @endif
                </div>

            </div>



            @php
    $paymentPayload = $overview['paymentAnalytics']
        ?? $paymentAnalytics
        ?? [];

    $statusChart = $paymentPayload['statusChart'] ?? [];
@endphp
            <div style="margin-top:10px;">
                <h4 style="font-size:11px;color:#444;margin-bottom:8px;">Status Totals</h4>
                <table class="table">
                    <thead><tr><th>Status</th><th>Count</th></tr></thead>
                    <tbody>
                        @foreach($statusChart as $status => $cnt)
                            <tr><td>{{ $status }}</td><td>{{ number_format((int)$cnt) }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if(!empty($includeRecordTables) && !empty($paymentsList))

<div style="margin-top:15px;">
    <h4 style="font-size:11px;color:#444;margin-bottom:8px;">
        Payment Records
    </h4>

    <table class="table" style="font-size:9px;">

        <thead class="bg-light text-uppercase small text-muted">
            <tr>
                <th>#</th>
                <th>Control No.</th>
                <th>Document Type</th>
                <th>Resident</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>

        <tbody>

            @foreach($paymentsList as $payment)

                <tr>

                    <td>{{ $loop->iteration }}</td>

                    <td>{{ $payment['control_no'] ?? '-' }}</td>

                    <td>{{ $payment['document_type'] ?? '-' }}</td>

                    <td>{{ $payment['resident_name'] ?? '-' }}</td>

                    <td>PHP {{ number_format((float)($payment['amount'] ?? 0), 2) }}</td>

                    <td>{{ $payment['status'] ?? '-' }}</td>

                    <td>{{ $payment['date'] ?? '-' }}</td>

                </tr>

            @endforeach

        </tbody>

    </table>

</div>

@endif
    @endif


    @if(isset($blotters['totalBlotters']))
        <div class="page-break"></div>

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
                    <div class="chart-label">Incident Types Distribution</div>
                    <img src="data:image/svg+xml;base64,{{ base64_encode($charts['incidentPie'] ?? '') }}">
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

        @if(!empty($includeRecordTables) && !empty($blottersList))

<div style="margin-top:15px;">

    <h4 style="font-size:11px;color:#444;margin-bottom:8px;">
        Blotter Records
    </h4>

    <table class="table" style="font-size:9px;">

        <thead class="bg-light text-uppercase small text-muted">
            <tr>
                <th>#</th>
                <th>Blotter No.</th>
                <th>Date</th>
                <th>Incident Type</th>
                <th>Complainant</th>
                <th>Respondent</th>
                <th>Location</th>
                <th>Status</th>
                <th>Has a Case?</th>
            </tr>
        </thead>

        <tbody>

        @foreach($blottersList as $blotter)

            <tr>

                <td>{{ $loop->iteration }}</td>

                <td>{{ $blotter['blotter_no'] ?? '-' }}</td>

                <td>
                    {{ !empty($blotter['incident_date'])
                        ? \Carbon\Carbon::parse($blotter['incident_date'])->format('M d, Y')
                        : '-' }}
                </td>

                <td>{{ $blotter['incident_type'] ?? '-' }}</td>

                <td>{{ $blotter['complainant_name'] ?? '-' }}</td>

                <td>{{ $blotter['respondent_name'] ?? '-' }}</td>

                <td>{{ $blotter['incident_location'] ?? '-' }}</td>

                <td>{{ ucfirst($blotter['status'] ?? '-') }}</td>

                <td>{{ !empty($blotter['has_case']) ? 'Yes' : 'No' }} {{ $blotter['case_no'] ? '/ ' . $blotter['case_no'] : '' }}</td>

            </tr>

        @endforeach

        </tbody>

    </table>

</div>

@endif
    @endif
   
    
    @if(isset($cases['totalCases']))
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
                The <strong>{{ $caseResolutionRate }}% resolution rate</strong> reflects closed/dismissed/settled cases.
                Currently open cases have been pending for an average of <strong>{{ number_format($avgDaysOpen, 1) }} days</strong>.
            </div>

            <div class="stats-grid">
                <div class="stat-box">
                    <div class="value">{{ number_format($totalCases) }}</div>
                    <div class="label">Total Cases</div>
                </div>
                <div class="stat-box">
                    <div class="value">{{ $caseResolutionRate }}%</div>
                    <div class="label">Resolution Rate</div>
                </div>
                <div class="stat-box">
                    <div class="value">{{ number_format($avgDaysOpen, 1) }}</div>
                    <div class="label">Avg. Days Open</div>
                </div>
            </div>

            {{-- Cases by Status --}}
            @if(isset($cases['caseStatusStats']) && is_array($cases['caseStatusStats']) && count($cases['caseStatusStats']) > 0)
                <div style="margin-top:12px;">
                    <h4 style="font-size:11px;color:#444;margin-bottom:8px;">Cases by Status</h4>
                    <table class="table">
                        <thead><tr><th>Status</th><th>Count</th></tr></thead>
                        <tbody>
                            @foreach($cases['caseStatusStats'] as $status => $cnt)
                                <tr>
                                    <td>{{ $status }}</td>
                                    <td>{{ number_format((int)$cnt) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Charts + Monthly trends --}}
            <div class="charts-grid" style="margin-top:12px;">
                @if(isset($charts['caseStatusPie']) && !empty($charts['caseStatusPie']))
                    <div class="chart-container">
                        <div class="chart-label">Cases by Status</div>
                        <img src="data:image/svg+xml;base64,{{ base64_encode($charts['caseStatusPie']) }}">
                    </div>
                @endif

                @if(isset($charts['caseTypePie']) && !empty($charts['caseTypePie']))
                    <div class="chart-container">
                        <div class="chart-label">Case Types</div>
                        <img src="data:image/svg+xml;base64,{{ base64_encode($charts['caseTypePie']) }}">
                    </div>
                @endif

                @if(isset($charts['caseMonthlyTrendArea']) && !empty($charts['caseMonthlyTrendArea']))
                    <div class="chart-container" style="grid-column: 1 / -1;">
                        <div class="chart-label">Monthly Case Trends</div>
                        <img src="data:image/svg+xml;base64,{{ base64_encode($charts['caseMonthlyTrendArea']) }}">
                    </div>
                @endif
            </div>

            {{-- Case Types summary (fallback if chart missing) --}}
            @if(!empty($caseTypeStats) && (empty($charts['caseTypePie']) || empty($charts['caseTypePie'])))
                <div class="highlight-box">
                    <h4>Case Types</h4>
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 6px 18px;">
                        @foreach(array_slice($caseTypeStats, 0, null, true) as $type => $count)
                            <div>
                                <strong>{{ $type }}</strong>: {{ number_format($count) }} cases
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        @if(!empty($includeRecordTables) && !empty($casesList))

<div style="margin-top:15px;">

    <h4 style="font-size:11px;color:#444;margin-bottom:8px;">
        Case Records
    </h4>

    <table class="table" style="font-size:9px;">

        <thead class="bg-light text-uppercase small text-muted">
            <tr>
                <th>#</th>
                <th>Case No.</th>
                <th>Blotter No.</th>
                <th>Complainant</th>
                <th>Respondent</th>
                <th>Status</th>
                <th>Opened</th>
            </tr>
        </thead>

        <tbody>

        @foreach($casesList as $case)

            <tr>

                <td>{{ $loop->iteration }}</td>

                <td>{{ $case['case_no'] ?? '-' }}</td>

                <td>{{ $case['blotter_no'] ?? '-' }}</td>

                <td>{{ $case['complainant_name'] ?? '-' }}</td>

                <td>{{ $case['respondent_name'] ?? '-' }}</td>

                <td>{{ $case['status'] ?? '-' }}</td>

                <td>{{ $case['opened_at'] ?? '-' }}</td>

            </tr>

        @endforeach

        </tbody>

    </table>

</div>

@endif
    @endif


    {{-- Officials --}}
    @php
    $officialRows = $officials['rows'] ?? [];
    $officialRowsCount = is_array($officialRows) ? count($officialRows) : 0;

    $hasOfficialSection =
        isset($officials) &&
        is_array($officials) &&
        $officialRowsCount > 0;
@endphp


    @if($hasOfficialSection)

        <div class="page-break"></div>

        <div class="section">
            <div class="section-title">Barangay Officials Overview</div>







            {{-- Officials members table (details like SQL) --}}

            @php
                $officialRows = $officials['rows'] ?? null;
            @endphp
            @if(is_array($officialRows) && count($officialRows) > 0)
                <div style="margin-top:12px;">
                    <h4 style="font-size:11px;color:#444;margin-bottom:8px;">Officials List</h4>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Committee</th>
                                <th>Term</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($officialRows as $row)
                                <tr>
                                    <td>{{ $row['name'] ?? '—' }}</td>
                                    <td>{{ $row['position'] ?? '—' }}</td>
                                    <td>{{ $row['committee'] ?? '—' }}</td>
                                    <td>{{ $row['term'] ?? '—' }}</td>
                                    <td>{{ $row['status'] ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

        </div>
    @endif

    {{-- Announcements & Events --}}
    @if(!empty($announcementsEvents) || !empty($totalAnnouncements ?? null) || !empty($totalEvents ?? null))

        <div class="page-break"></div>
        <div class="section">
            <div class="section-title">Announcements & Events Overview</div>
            <div class="summary-text">Counts and distributions of announcement types and event types (selected period).</div>

            <div class="stats-grid" style="margin-top:10px;">
                <div class="stat-box">
                    <div class="value">{{ number_format($announcementsEvents['totalAnnouncements'] ?? 0) }}</div>
                    <div class="label">Total Announcements</div>
                </div>
 
                <div class="stat-box">
                    <div class="value">{{ number_format($announcementsEvents['totalEvents'] ?? 0) }}</div>
                    <div class="label">Total Events</div>
                </div>
            </div>

            <div class="charts-grid" style="margin-top:10px;">
                <div class="chart-container">
                    <div class="chart-label">Announcement Types</div>
                    @if(isset($charts['announcementTypePie']) && !empty($charts['announcementTypePie']))
                        <img src="data:image/svg+xml;base64,{{ base64_encode($charts['announcementTypePie']) }}">
                    @else
                        <div class="summary-text">Chart not available (announcementTypePie).</div>
                    @endif
                </div>

                <div class="chart-container">
                    <div class="chart-label">Event Types</div>
                    @if(isset($charts['eventTypeBar']) && !empty($charts['eventTypeBar']))
                        <img src="data:image/svg+xml;base64,{{ base64_encode($charts['eventTypeBar']) }}">
                    @else
                        <div class="summary-text">Chart not available (eventTypeBar).</div>
                    @endif
                </div>
            </div>

            <div class="two-col" style="margin-top:10px;">
                <div>
                    <h4 style="font-size:11px;color:#444;margin-bottom:8px;">Announcement Types (Top)</h4>
                    <table class="table">
                        <thead><tr><th>Type</th><th>Count</th></tr></thead>
                        <tbody>
                            @foreach(array_slice($announcementsEvents['announcementTypes'] ?? [], 0, 8, true) as $t => $cnt)
                                <tr><td>{{ $t }}</td><td>{{ number_format((int)$cnt) }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div>
                    <h4 style="font-size:11px;color:#444;margin-bottom:8px;">Event Types (Top)</h4>
                    <table class="table">
                        <thead><tr><th>Type</th><th>Count</th></tr></thead>
                        <tbody>
                            @foreach(array_slice($announcementsEvents['eventTypes'] ?? [], 0, 8, true) as $t => $cnt)
                                <tr><td>{{ $t }}</td><td>{{ number_format((int)$cnt) }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @if(!empty($includeRecordTables) && !empty($announcementsList))

<div style="margin-top:15px;">

    <h4 style="font-size:11px;color:#444;margin-bottom:8px;">
        Announcements Records
    </h4>

    <table class="table" style="font-size:9px;">

        <thead class="bg-light text-uppercase small text-muted">
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Type</th>
                <th>Content</th>
                <th>Publish Date</th>
            </tr>
        </thead>

        <tbody>

        @foreach($announcementsList as $announcement)

            <tr>

                <td>{{ $loop->iteration }}</td>

                <td>{{ $announcement['title'] ?? '-' }}</td>

                <td>{{ $announcement['type'] ?? '-' }}</td>

                <td>{{ \Illuminate\Support\Str::limit(strip_tags($announcement['content'] ?? '-'), 80) }}</td>

                <td>
                    {{ !empty($announcement['publish_date'])
                        ? \Carbon\Carbon::parse($announcement['publish_date'])->format('M d, Y')
                        : '-' }}
                </td>

            </tr>

        @endforeach

        </tbody>

    </table>

</div>

@endif
@if(!empty($includeRecordTables) && !empty($eventsList))

<div style="margin-top:20px;">

    <h4 style="font-size:11px;color:#444;margin-bottom:8px;">
        Event Records
    </h4>

    <table class="table" style="font-size:9px;">

        <thead class="bg-light text-uppercase small text-muted">
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Type</th>
                <th>Description</th>
                <th>Location</th>
                <th>Start</th>
                <th>End</th>
                <th>Created By</th>
            </tr>
        </thead>

        <tbody>

        @foreach($eventsList as $event)

            <tr>

                <td>{{ $loop->iteration }}</td>

                <td>{{ $event['title'] ?? '-' }}</td>

                <td>{{ $event['type'] ?? '-' }}</td>

                <td>{{ \Illuminate\Support\Str::limit(strip_tags($event['description'] ?? '-'), 80) }}</td>

                <td>{{ $event['location'] ?? '-' }}</td>

                <td>
                    {{ !empty($event['start_datetime'])
                        ? \Carbon\Carbon::parse($event['start_datetime'])->format('M d, Y h:i A')
                        : '-' }}
                </td>

                <td>
                    {{ !empty($event['end_datetime'])
                        ? \Carbon\Carbon::parse($event['end_datetime'])->format('M d, Y h:i A')
                        : '-' }}
                </td>

                <td>{{ $event['created_by'] ?? '-' }}</td>

            </tr>

        @endforeach

        </tbody>

    </table>

</div>

@endif
    @endif

    {{-- Users & Audit --}}
   @if(!empty($usersAdminsAudit))

        <div class="page-break"></div>
        <div class="section">
            <div class="section-title">Users, Admins & Audit Overview</div>
            <div class="summary-text">User roles, audit totals, and top modified modules / actions (selected period).</div>

            <div class="stats-grid" style="margin-top:10px;">
                <div class="stat-box">
                    <div class="value">{{ number_format($usersAdminsAudit['totalUsers'] ?? 0) }}</div>
                    <div class="label">Total Users</div>
                </div>
                <div class="stat-box">
                    <div class="value">{{ number_format($usersAdminsAudit['activeUsers'] ?? 0) }}</div>
                    <div class="label">Active Users</div>
                </div>
                <div class="stat-box">
                    <div class="value">{{ number_format($usersAdminsAudit['inactiveUsers'] ?? 0) }}</div>
                    <div class="label">Inactive Users</div>
                </div>
                <div class="stat-box">
                    <div class="value">{{ number_format($usersAdminsAudit['totalAdmins'] ?? 0) }}</div>
                    <div class="label">Total Admins</div>
                </div>
                <div class="stat-box">
                    <div class="value">{{ number_format($usersAdminsAudit['totalAuditLogs'] ?? 0) }}</div>
                    <div class="label">Total Audit Logs</div>
                </div>
            </div>

            <div class="charts-grid" style="margin-top:10px;">
                <div class="chart-container">
                    <div class="chart-label">User Roles Distribution</div>
                    @if(isset($charts['userRolePie']) && !empty($charts['userRolePie']))
                        <img src="data:image/svg+xml;base64,{{ base64_encode($charts['userRolePie']) }}">
                    @else
                        <div class="summary-text">Chart not available (userRolePie).</div>
                    @endif
                </div>
                <div class="chart-container">
                    <div class="chart-label">Most Common Actions</div>
                    @if(isset($charts['auditActionDonut']) && !empty($charts['auditActionDonut']))
                        <img src="data:image/svg+xml;base64,{{ base64_encode($charts['auditActionDonut']) }}">
                    @else
                        <div class="summary-text">Chart not available (auditActionDonut).</div>
                    @endif
                </div>
            </div>

            <div class="two-col" style="margin-top:10px;">
                <div>
                    <h4 style="font-size:11px;color:#444;margin-bottom:8px;">Most Modified Modules (Top)</h4>
                    <table class="table">
                        <thead><tr><th>Module</th><th>Count</th></tr></thead>
                        <tbody>
                           @foreach(array_slice($usersAdminsAudit['mostModifiedModules'] ?? [], 0, 8, true) as $m => $cnt)
                                <tr><td>{{ $m }}</td><td>{{ number_format((int)$cnt) }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div>
                    <h4 style="font-size:11px;color:#444;margin-bottom:8px;">Most Common Actions (Top)</h4>
                    <table class="table">
                        <thead><tr><th>Action</th><th>Count</th></tr></thead>
                        <tbody>
                            @foreach(array_slice($usersAdminsAudit['mostCommonActions'] ?? [], 0, 8, true) as $a => $cnt)
                                <tr><td>{{ $a }}</td><td>{{ number_format((int)$cnt) }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @if($includeRecordTables && !empty($auditLogsList))

<div style="margin-top:15px;">

    <h4 style="font-size:11px;color:#444;margin-bottom:8px;">
        Activity Logs
    </h4>

    <table class="table" style="font-size:9px;">

        <thead class="bg-light text-uppercase small text-muted">

            <tr>
                <th>#</th>
                <th>Date / Time</th>
                <th>User</th>
                <th>Module</th>
                <th>Action</th>
                <th>Record ID</th>
                <th>IP Address</th>
            </tr>

        </thead>

        <tbody>

        @foreach($auditLogsList as $log)

            <tr>

                <td>{{ $loop->iteration }}</td>

                <td>{{ $log['datetime'] }}</td>

                <td>
                    {{ $log['user'] }}

                    @if(!empty($log['email']))
                        <br>
                        <small>{{ $log['email'] }}</small>
                    @endif
                </td>

                <td>{{ $log['module'] }}</td>

                <td>{{ $log['action'] }}</td>

                <td>{{ $log['record_id'] ?? '-' }}</td>

                <td>{{ $log['ip_address'] ?? '-' }}</td>

            </tr>

        @endforeach

        </tbody>

    </table>

</div>

@endif
    @endif

    



    <!-- Footer inside each page causes a trailing blank page in DomPDF.
         Keep it out of the fixed footer to avoid blank first/extra pages.
         (If you need a footer, re-add using DomPDF-compatible margin boxes.)
    -->


</body>
</html>




