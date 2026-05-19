@extends('layouts.admin')

@section('title', 'Analytics Dashboard')

@section('styles')
<style>
    .stat-card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .stat-icon { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
    .stat-value { font-size: 2rem; font-weight: 700; color: #1f2937; }
    .stat-label { font-size: 0.875rem; color: #6b7280; }
    .growth-badge { font-size: 0.75rem; padding: 4px 10px; border-radius: 999px; font-weight: 600; }
    .growth-up { background: #dcfce7; color: #166534; }
    .growth-down { background: #fee2e2; color: #991b1b; }
    .section-title { font-size: 1.125rem; font-weight: 700; color: #1f2937; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    .chart-card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px; }
    .filter-card { background: white; border-radius: 16px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px; }
    .tab-btn { padding: 10px 20px; border: none; background: transparent; color: #6b7280; font-weight: 500; border-bottom: 2px solid transparent; transition: all 0.2s; }
    .tab-btn.active { color: #1055C9; border-bottom-color: #1055C9; }
    .tab-btn:hover { color: #1055C9; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { padding: 12px 16px; text-align: left; font-weight: 600; color: #374151; background: #f9fafb; border-bottom: 1px solid #e5e7eb; font-size: 0.875rem; }
    .data-table td { padding: 12px 16px; border-bottom: 1px solid #e5e7eb; font-size: 0.875rem; }
    .data-table tr:hover { background: #f9faff; }
    .status-badge { padding: 4px 12px; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
    .status-active, .status-verified, .status-approved { background: #dcfce7; color: #166534; }
    .status-inactive, .status-rejected { background: #fee2e2; color: #991b1b; }
    .status-pending { background: #fef3c7; color: #92400e; }
    .status-resolved { background: #dcfce7; color: #166534; }
    .status-filed, .status-open { background: #dbeafe; color: #1e40af; }
    .activity-item { padding: 12px 0; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; gap: 12px; }
    .activity-item:last-child { border-bottom: none; }
    .activity-icon { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; }
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .page-title { font-size: 1.5rem; font-weight: 700; color: #1f2937; }
</style>
@endsection

@section('content')
<div class="container-fluid p-4">
        <div class="page-header">
            <div>
                <h1 class="page-title">Analytics Dashboard</h1>
                <p class="text-muted">Comprehensive analytics and reports for Barangay Bucandala 1</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#exportModal">
                    <i class="bi bi-download me-2"></i>Export
                </button>
            </div>
        </div>

        <!-- Filter Panel -->
        <div class="filter-card">
            <form method="GET" action="/admin/analytics" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-2"></i>Filter
                    </button>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Quick Dates</label>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('today')">Today</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('week')">This Week</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('month')">This Month</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('year')">This Year</button>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearDateRange()">Clear</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Export Modal -->
        <div class="modal fade" id="exportModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Export Analytics</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Date From</label>
                            <input type="date" id="exportDateFrom" class="form-control" value="{{ $dateFrom }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date To</label>
                            <input type="date" id="exportDateTo" class="form-control" value="{{ $dateTo }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quick Dates</label>
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setExportDate('today')">Today</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setExportDate('week')">This Week</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setExportDate('month')">This Month</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setExportDate('year')">This Year</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setExportDate('all')">All Time</button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Export Format</label>
                            <select id="exportFormat" class="form-select">
                                <option value="pdf">PDF (with charts)</option>
                                <option value="csv">CSV (data only)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="confirmExport()">
                            <i class="bi bi-download me-2"></i>Export
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overview Stats -->
        <div class="row g-3 mb-4">

            <!-- Total Residents -->
            <div class="col-md-2 col-sm-4 col-6">
                <div class="stat-card h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-value">{{ number_format($totalResidents) }}</div>
                            <div class="stat-label">Total Residents</div>
                        </div>
                        <div class="stat-icon" style="background: #dbeafe; color: #1e40af;">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                    @if(isset($residentGrowth) && $residentGrowth !== 0)
                    <div class="mt-2">
                        <span class="growth-badge {{ $residentGrowth > 0 ? 'growth-up' : 'growth-down' }}">
                            {{ $residentGrowth > 0 ? '+' : '' }}{{ $residentGrowth }}%
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Households -->
            <div class="col-md-2 col-sm-4 col-6">
                <div class="stat-card h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-value">{{ number_format($totalHouseholds) }}</div>
                            <div class="stat-label">Households</div>
                        </div>
                        <div class="stat-icon" style="background: #dcfce7; color: #166534;">
                            <i class="bi bi-house"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Document Requests -->
            <div class="col-md-2 col-sm-4 col-6">
                <div class="stat-card h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-value">{{ number_format($totalDocumentRequests) }}</div>
                            <div class="stat-label">Document Requests</div>
                        </div>
                        <div class="stat-icon" style="background: #fef3c7; color: #92400e;">
                            <i class="bi bi-file-earmark"></i>
                        </div>
                    </div>
                    @if(isset($documentRequestGrowth) && $documentRequestGrowth !== 0)
                    <div class="mt-2">
                        <span class="growth-badge {{ $documentRequestGrowth > 0 ? 'growth-up' : 'growth-down' }}">
                            {{ $documentRequestGrowth > 0 ? '+' : '' }}{{ $documentRequestGrowth }}%
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Blotters -->
            <div class="col-md-2 col-sm-4 col-6">
                <div class="stat-card h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-value">{{ number_format($totalBlotters) }}</div>
                            <div class="stat-label">Blotters</div>
                        </div>
                        <div class="stat-icon" style="background: #fee2e2; color: #991b1b;">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                    </div>
                    @if(isset($blotterGrowth) && $blotterGrowth !== 0)
                    <div class="mt-2">
                        <span class="growth-badge {{ $blotterGrowth > 0 ? 'growth-down' : 'growth-up' }}">
                            {{ $blotterGrowth > 0 ? '+' : '' }}{{ $blotterGrowth }}%
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Cases -->
            <div class="col-md-2 col-sm-4 col-6">
                <div class="stat-card h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-value">{{ number_format($totalCases) }}</div>
                            <div class="stat-label">Cases</div>
                        </div>
                        <div class="stat-icon" style="background: #f3e8ff; color: #7c3aed;">
                            <i class="bi bi-briefcase"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pets -->
            <div class="col-md-2 col-sm-4 col-6">
                <div class="stat-card h-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="stat-value">{{ number_format($totalPets) }}</div>
                            <div class="stat-label">Registered Pets</div>
                        </div>
                        <div class="stat-icon" style="background: #fed7aa; color: #c2410c;">
                            <i class="bi bi-heart"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics Tabs -->
        <ul class="nav nav-tabs mb-4" id="analyticsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="tab-btn active" id="residents-tab" data-bs-toggle="tab" data-bs-target="#residents" type="button">
                    <i class="bi bi-people me-2"></i>Residents
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="tab-btn" id="households-tab" data-bs-toggle="tab" data-bs-target="#households" type="button">
                    <i class="bi bi-house me-2"></i>Households
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="tab-btn" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button">
                    <i class="bi bi-file-earmark me-2"></i>Documents
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="tab-btn" id="blotter-tab" data-bs-toggle="tab" data-bs-target="#blotter" type="button">
                    <i class="bi bi-exclamation-triangle me-2"></i>Blotters
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="tab-btn" id="cases-tab" data-bs-toggle="tab" data-bs-target="#cases" type="button">
                    <i class="bi bi-briefcase me-2"></i>Cases
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="tab-btn" id="pets-tab" data-bs-toggle="tab" data-bs-target="#pets" type="button">
                    <i class="bi bi-heart me-2"></i>Pets
                </button>
            </li>
        </ul>

        <div class="tab-content" id="analyticsTabsContent">
            <!-- Residents Analytics -->
            <div class="tab-pane fade show active" id="residents" role="tabpanel">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-person-badge"></i>Gender Distribution</h6>
                            <canvas id="residentGenderChart" height="250"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-bar-chart"></i>Age Groups</h6>
                            <canvas id="residentAgeChart" height="250"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-graph-up"></i>Monthly Registration Trends</h6>
                            <canvas id="residentTrendChart" height="250"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-pie-chart"></i>Civil Status Distribution</h6>
                            <canvas id="civilStatusChart" height="250"></canvas>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-briefcase"></i>Top Occupations</h6>
                            <table class="data-table">
                                <thead>
                                    <tr><th>Occupation</th><th>Count</th><th>Percentage</th></tr>
                                </thead>
                                <tbody>
                                    @foreach($occupationStats as $occupation => $count)
                                    <tr>
                                        <td>{{ $occupation ?: 'Not Specified' }}</td>
                                        <td>{{ number_format($count) }}</td>
                                        <td>{{ round(($count / max($totalResidents, 1)) * 100) }}%</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Households Analytics -->
            <div class="tab-pane fade" id="households" role="tabpanel">
                <div class="row g-3">
                     <div class="col-md-4">
                         <div class="stat-card text-center h-100">
                             <div class="stat-value">{{ number_format($totalHouseholds) }}</div>
                             <div class="stat-label">Total Households</div>
                         </div>
                     </div>
                     <div class="col-md-4">
                         <div class="stat-card text-center h-100">
                             <div class="stat-value">{{ number_format($avgMembers, 1) }}</div>
                             <div class="stat-label">Avg Members per Household</div>
                         </div>
                     </div>
                     <div class="col-md-4">
                         <div class="stat-card text-center h-100">
                             <div class="stat-value">{{ number_format($fourPsCount) }}</div>
                             <div class="stat-label">4Ps Beneficiary Households</div>
                         </div>
                     </div>
                      <div class="col-md-6">
                          <div class="chart-card">
                              <h6 class="section-title"><i class="bi bi-geo-alt"></i>Households by Street</h6>
                              <canvas id="householdZoneChart" height="250"></canvas>
                          </div>
                      </div>
                       <div class="col-md-6">
                           <div class="chart-card">
                               <h6 class="section-title"><i class="bi bi-house"></i>Household Types</h6>
                               <canvas id="householdTypeChart" height="250"></canvas>
                           </div>
                       </div>
                       <div class="col-md-6">
                           <div class="chart-card">
                               <h6 class="section-title"><i class="bi bi-building"></i>Households by Phase</h6>
                               <canvas id="householdPhaseChart" height="250"></canvas>
                           </div>
                       </div>
                       <div class="col-md-4">
                          <div class="stat-card text-center h-100">
                              <div class="stat-value">{{ number_format($indigentCount) }}</div>
                              <div class="stat-label">Indigent Households</div>
                          </div>
                      </div>
                      <div class="col-md-4">
                          <div class="stat-card text-center h-100">
                              <div class="stat-value">{{ number_format($seniorCount) }}</div>
                              <div class="stat-label">Households with Seniors</div>
                          </div>
                      </div>
                      <div class="col-md-4">
                          <div class="stat-card text-center h-100">
                              <div class="stat-value">{{ number_format($pwdCount) }}</div>
                              <div class="stat-label">Households with PWDs</div>
                          </div>
                      </div>
                    <div class="col-md-6">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-graph-up"></i>Monthly Household Trends</h6>
                            <canvas id="householdTrendChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Document Request Analytics -->
            <div class="tab-pane fade" id="documents" role="tabpanel">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="stat-card text-center h-100">
                            <div class="stat-value">{{ number_format($totalDocumentRequests) }}</div>
                            <div class="stat-label">Total Requests</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card text-center h-100">
                            <div class="stat-value">{{ number_format($avgProcessingDays, 1) }}</div>
                            <div class="stat-label">Avg Processing Days</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card text-center h-100">
                            <div class="stat-value">{{ $approvalRate }}%</div>
                            <div class="stat-label">Approval Rate</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-pie-chart"></i>Request by Status</h6>
                            <canvas id="docStatusChart" height="250"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-file-earmark-text"></i>Top Document Types</h6>
                            <canvas id="docTypeChart" height="250"></canvas>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-graph-up"></i>Monthly Request Trends</h6>
                            <canvas id="docTrendChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Blotter Analytics -->
            <div class="tab-pane fade" id="blotter" role="tabpanel">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="stat-card text-center h-100">
                            <div class="stat-value">{{ number_format($totalBlotters) }}</div>
                            <div class="stat-label">Total Blotters</div>
                        </div>
                     </div>
                     <div class="col-md-4">
                         <div class="stat-card text-center h-100">
                             <div class="stat-value">{{ $blotterResolutionRate }}%</div>
                             <div class="stat-label">Resolution Rate</div>
                         </div>
                     </div>
                     <div class="col-md-4">
                         <div class="stat-card text-center h-100">
                             <div class="stat-value">{{ number_format($avgResolutionDays, 1) }}</div>
                             <div class="stat-label">Avg Resolution Days</div>
                         </div>
                     </div>
                     <div class="col-md-6">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-diagram-3"></i>Case Conversion</h6>
                            <canvas id="caseConversionChart" height="250"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-exclamation-triangle"></i>Incident Types</h6>
                            <canvas id="incidentTypeChart" height="250"></canvas>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-graph-up"></i>Monthly Incident Trends</h6>
                            <canvas id="blotterTrendChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Case Analytics -->
            <div class="tab-pane fade" id="cases" role="tabpanel">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="stat-card text-center h-100">
                            <div class="stat-value">{{ number_format($totalCases) }}</div>
                            <div class="stat-label">Total Cases</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card text-center h-100">
                            <div class="stat-value">{{ $resolutionRate }}%</div>
                            <div class="stat-label">Resolution Rate</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card text-center h-100">
                            <div class="stat-value">{{ number_format($avgDaysOpen, 0) }}</div>
                            <div class="stat-label">Avg Days Open</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-pie-chart"></i>Cases by Status</h6>
                            <canvas id="caseStatusChart" height="250"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-briefcase"></i>Case Types</h6>
                            <canvas id="caseTypeChart" height="250"></canvas>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-graph-up"></i>Monthly Case Trends</h6>
                            <canvas id="caseTrendChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pet Analytics -->
            <div class="tab-pane fade" id="pets" role="tabpanel">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="stat-card text-center h-100">
                            <div class="stat-value">{{ number_format($totalPets) }}</div>
                            <div class="stat-label">Total Pets</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card text-center h-100">
                            <div class="stat-value">{{ ($vaccinationStats['up-to-date'] ?? 0) }}</div>
                            <div class="stat-label">Up to Date</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card text-center h-100">
                            <div class="stat-value">{{ (($vaccinationStats['overdue'] ?? 0) + ($vaccinationStats['not-vaccinated'] ?? 0)) }}</div>
                            <div class="stat-label">Not Vaccinated</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-pie-chart"></i>Pets by Type</h6>
                            <canvas id="petTypeChart" height="250"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-heart"></i>Vaccination Status</h6>
                            <canvas id="vaccinationChart" height="250"></canvas>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-graph-up"></i>Monthly Registration Trends</h6>
                            <canvas id="petTrendChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row g-3 mt-4">
            <div class="col-md-6">
                <div class="chart-card">
                    <h6 class="section-title"><i class="bi bi-clock-history"></i>Recent Activities</h6>
                    @foreach($recentActivities as $activity)
                    <div class="activity-item">
                        <div class="activity-icon" style="background: {{ $activity['type'] === 'resident' ? '#dbeafe' : ($activity['type'] === 'document' ? '#fef3c7' : '#fee2e2') }}; color: {{ $activity['type'] === 'resident' ? '#1e40af' : ($activity['type'] === 'document' ? '#92400e' : '#991b1b') }};">
                            <i class="bi {{ $activity['type'] === 'resident' ? 'bi-person' : ($activity['type'] === 'document' ? 'bi-file' : 'bi-exclamation') }}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $activity['title'] }}</div>
                            <div class="text-muted small">{{ $activity['description'] }}</div>
                        </div>
                        <div class="text-muted small">{{ $activity['created_at']->diffForHumans() }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="col-md-6">
                <div class="chart-card">
                    <h6 class="section-title"><i class="bi bi-pie-chart"></i>Status Overview</h6>
                    <div class="row g-3">
                    <div class="col-6">
                        <div class="p-3 rounded" style="background: #eff6ff;">
                            <div class="fw-bold text-primary">{{ ($statusStats['active'] ?? 0) }}</div>
                            <div class="small text-muted">Active Residents</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded" style="background: #fee2e2;">
                            <div class="fw-bold text-danger">{{ $inactiveCount }}</div>
                            <div class="small text-muted">Archived</div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        try {
            console.log('Analytics: DOM loaded, Chart type:', typeof Chart);
            if (typeof Chart === 'undefined') {
                console.error('Chart.js is not loaded');
                return;
            }
        Chart.defaults.font.family = "'Segoe UI', system-ui, sans-serif";
        Chart.defaults.color = '#6b7280';

        // Color palette
        const colors = {
            blue: '#1055C9',
            green: '#10b981',
            yellow: '#F59E0B',
            red: '#EF4444',
            purple: '#8B5CF6',
            orange: '#F97316',
            pink: '#EC4899',
            cyan: '#06B6D4',
            gray: '#6B7280',
        };

        // Helper to create month labels
        function getMonthLabels() {
            return ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        }

        // Helper to fill missing months in data
        function fillMonthlyData(data) {
            const months = [0,0,0,0,0,0,0,0,0,0,0,0];
            for (const [month, count] of Object.entries(data)) {
                months[parseInt(month) - 1] = count;
            }
            return months;
        }

        // Residents Gender Chart
        const residentGender = new Chart(document.getElementById('residentGenderChart'), {
            type: 'doughnut',
            data: {
                labels: ['Male', 'Female'],
                datasets: [{
                    data: [{{ $genderStats['male'] ?? 0 }}, {{ $genderStats['female'] ?? 0 }}],
                    backgroundColor: [colors.blue, colors.pink],
                    borderWidth: 0
                }]
            }
        });

        // Residents Age Chart
        const residentAge = new Chart(document.getElementById('residentAgeChart'), {
            type: 'bar',
            data: {
                labels: ['0-17', '18-30', '31-50', '51-65', '65+'],
                datasets: [{
                    label: 'Residents',
                    data: [{{ $ageGroups['0-17'] ?? 0 }}, {{ $ageGroups['18-30'] ?? 0 }}, {{ $ageGroups['31-50'] ?? 0 }}, {{ $ageGroups['51-65'] ?? 0 }}, {{ $ageGroups['65+'] ?? 0 }}],
                    backgroundColor: colors.blue,
                    borderRadius: 6
                }]
            },
            options: { scales: { y: { beginAtZero: true } } }
        });

        // Residents Trend Chart
        const residentTrend = new Chart(document.getElementById('residentTrendChart'), {
            type: 'line',
            data: {
                labels: getMonthLabels(),
                datasets: [{
                    label: 'Registrations',
                    data: fillMonthlyData(@json($residentMonthlyTrends)),
                    borderColor: colors.blue,
                    backgroundColor: 'rgba(16, 85, 201, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: { scales: { y: { beginAtZero: true } } }
        });

        // Civil Status Chart
        const civilStatus = new Chart(document.getElementById('civilStatusChart'), {
            type: 'pie',
            data: {
                labels: @json(array_keys($civilStatusStats)),
                datasets: [{
                    data: @json(array_values($civilStatusStats)),
                    backgroundColor: [colors.blue, colors.green, colors.yellow, colors.purple, colors.orange],
                    borderWidth: 0
                }]
            }
        });

        // Household Street Chart
        const householdZone = new Chart(document.getElementById('householdZoneChart'), {
            type: 'bar',
            data: {
                labels: @json(array_keys($streetStats)),
                datasets: [{
                    label: 'Households',
                    data: @json(array_values($streetStats)),
                    backgroundColor: colors.green,
                    borderRadius: 6
                }]
            },
            options: { indexAxis: 'y', scales: { x: { beginAtZero: true } } }
        });

        // Household Type Chart
        const householdType = new Chart(document.getElementById('householdTypeChart'), {
            type: 'pie',
            data: {
                labels: @json(array_keys($householdTypeStats)),
                datasets: [{
                    data: @json(array_values($householdTypeStats)),
                    backgroundColor: [colors.blue, colors.green, colors.yellow],
                    borderWidth: 0
                }]
            }
        });

        // Household Phase Chart
        const householdPhase = new Chart(document.getElementById('householdPhaseChart'), {
            type: 'bar',
            data: {
                labels: @json(array_keys($phaseStats)),
                datasets: [{
                    label: 'Households',
                    data: @json(array_values($phaseStats)),
                    backgroundColor: colors.purple,
                    borderRadius: 6
                }]
            },
            options: { indexAxis: 'y', scales: { x: { beginAtZero: true } } }
        });

        // Household Trend Chart
        const householdTrend = new Chart(document.getElementById('householdTrendChart'), {
            type: 'line',
            data: {
                labels: getMonthLabels(),
                datasets: [{
                    label: 'New Households',
                    data: fillMonthlyData(@json($householdMonthlyTrends)),
                    borderColor: colors.green,
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: { scales: { y: { beginAtZero: true } } }
        });

        // Document Status Chart
        const docStatus = new Chart(document.getElementById('docStatusChart'), {
            type: 'doughnut',
            data: {
                labels: @json(array_keys($docStatusStats)),
                datasets: [{
                    data: @json(array_values($docStatusStats)),
                    backgroundColor: [colors.yellow, colors.green, colors.red, colors.blue],
                    borderWidth: 0
                }]
            }
        });

        // Document Type Chart
        const docType = new Chart(document.getElementById('docTypeChart'), {
            type: 'bar',
            data: {
                labels: @json(array_keys($topTypes)),
                datasets: [{
                    label: 'Requests',
                    data: @json(array_values($topTypes)),
                    backgroundColor: colors.yellow,
                    borderRadius: 6
                }]
            },
            options: { indexAxis: 'y', scales: { x: { beginAtZero: true } } }
        });

        // Document Trend Chart
        const docTrend = new Chart(document.getElementById('docTrendChart'), {
            type: 'line',
            data: {
                labels: getMonthLabels(),
                datasets: [{
                    label: 'Requests',
                    data: fillMonthlyData(@json($docMonthlyTrends)),
                    borderColor: colors.yellow,
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: { scales: { y: { beginAtZero: true } } }
        });

        // Incident Type Chart
        const incidentType = new Chart(document.getElementById('incidentTypeChart'), {
            type: 'bar',
            data: {
                labels: @json(array_keys($incidentTypeStats)),
                datasets: [{
                    label: 'Incidents',
                    data: @json(array_values($incidentTypeStats)),
                    backgroundColor: colors.red,
                    borderRadius: 6
                }]
            },
            options: { indexAxis: 'y', scales: { x: { beginAtZero: true } } }
        });

        // Case Conversion Chart
        const caseConversion = new Chart(document.getElementById('caseConversionChart'), {
            type: 'doughnut',
            data: {
                labels: ['With Case', 'No Case'],
                datasets: [{
                    data: [@json($withCase), @json($withoutCase)],
                    backgroundColor: [colors.purple, colors.gray],
                    borderWidth: 0
                }]
            }
        });

        // Blotter Trend Chart
        const blotterTrend = new Chart(document.getElementById('blotterTrendChart'), {
            type: 'line',
            data: {
                labels: getMonthLabels(),
                datasets: [{
                    label: 'Incidents',
                    data: fillMonthlyData(@json($blotterMonthlyTrends)),
                    borderColor: colors.red,
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: { scales: { y: { beginAtZero: true } } }
        });

        // Case Status Chart
        const caseStatus = new Chart(document.getElementById('caseStatusChart'), {
            type: 'doughnut',
            data: {
                labels: @json(array_keys($caseStatusStats)),
                datasets: [{
                    data: @json(array_values($caseStatusStats)),
                    backgroundColor: [colors.purple, colors.yellow, colors.green, colors.red],
                    borderWidth: 0
                }]
            }
        });

        // Case Type Chart
        const caseType = new Chart(document.getElementById('caseTypeChart'), {
            type: 'bar',
            data: {
                labels: @json(array_keys($caseTypeStats)),
                datasets: [{
                    label: 'Cases',
                    data: @json(array_values($caseTypeStats)),
                    backgroundColor: colors.purple,
                    borderRadius: 6
                }]
            },
            options: { indexAxis: 'y', scales: { x: { beginAtZero: true } } }
        });

        // Case Trend Chart
        const caseTrend = new Chart(document.getElementById('caseTrendChart'), {
            type: 'line',
            data: {
                labels: getMonthLabels(),
                datasets: [{
                    label: 'Cases',
                    data: fillMonthlyData(@json($caseMonthlyTrends)),
                    borderColor: colors.purple,
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: { scales: { y: { beginAtZero: true } } }
        });

        // Pet Type Chart
        const petType = new Chart(document.getElementById('petTypeChart'), {
            type: 'doughnut',
            data: {
                labels: @json(array_keys($typeStats)),
                datasets: [{
                    data: @json(array_values($typeStats)),
                    backgroundColor: [colors.orange, colors.pink, colors.cyan, colors.green],
                    borderWidth: 0
                }]
            }
        });

        // Vaccination Chart
        const vaccination = new Chart(document.getElementById('vaccinationChart'), {
            type: 'pie',
            data: {
                labels: ['Up to Date', 'Overdue', 'Not Vaccinated'],
                datasets: [{
                    data: [
                        @json($vaccinationStats['up-to-date'] ?? 0),
                        @json($vaccinationStats['overdue'] ?? 0),
                        @json($vaccinationStats['not-vaccinated'] ?? 0)
                    ],
                    backgroundColor: [colors.green, colors.yellow, colors.red],
                    borderWidth: 0
                }]
            }
        });

        // Pet Trend Chart
        const petTrendChart = new Chart(document.getElementById('petTrendChart'), {
            type: 'line',
            data: {
                labels: getMonthLabels(),
                datasets: [{
                    label: 'Registrations',
                    data: fillMonthlyData(@json($petMonthlyTrends)),
                    borderColor: colors.orange,
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: { scales: { y: { beginAtZero: true } } }
        });

        // Store all charts for resize on tab switch
        const charts = {
            residentGender,
            residentAge,
            residentTrend,
            civilStatus,
            householdZone,
            householdTrend,
            docStatus,
            docType,
            docTrend,
            incidentType,
            blotterTrend,
            caseConversion,
            caseStatus,
            caseType,
            caseTrend,
            petType,
            vaccination,
            petTrendChart
        };

        // Resize charts when tab becomes visible
        const tabButtons = document.querySelectorAll('#analyticsTabs button[data-bs-toggle="tab"]');
        tabButtons.forEach(button => {
            button.addEventListener('shown.bs.tab', event => {
                setTimeout(() => {
                    const targetPane = document.querySelector(button.dataset.bsTarget);
                    if (targetPane) {
                        const canvases = targetPane.querySelectorAll('canvas');
                        canvases.forEach(canvas => {
                            const chartId = canvas.id;
                            const chart = Object.values(charts).find(c => c && c.canvas === canvas);
                            if (chart) chart.resize();
                        });
                    }
                }, 100);
            });
        });

        // Date presets - make globally accessible
        window.setDateRange = function(range) {
            const today = new Date();
            let from, to;
            switch(range) {
                case 'today':
                    from = to = today.toISOString().split('T')[0];
                    break;
                case 'week':
                    from = new Date(today.getFullYear(), today.getMonth(), today.getDate() - 7).toISOString().split('T')[0];
                    to = new Date().toISOString().split('T')[0];
                    break;
                case 'month':
                    from = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                    to = new Date().toISOString().split('T')[0];
                    break;
                case 'year':
                    from = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
                    to = new Date().toISOString().split('T')[0];
                    break;
            }
            document.querySelector('input[name="date_from"]').value = from;
            document.querySelector('input[name="date_to"]').value = to;
            // Navigate directly to the URL instead of form submit
            window.location.href = '/admin/analytics?date_from=' + from + '&date_to=' + to;
        };

        // Clear date range
        window.clearDateRange = function() {
            document.querySelector('input[name="date_from"]').value = '';
            document.querySelector('input[name="date_to"]').value = '';
            window.location.href = '/admin/analytics';
        };

        // Export function - make globally accessible
        window.exportData = function(format = 'csv') {
            const dateFrom = document.querySelector('input[name="date_from"]').value;
            const dateTo = document.querySelector('input[name="date_to"]').value;
            
            let url = '{{ route('admin.analytics.export') }}?format=' + format;
            if (dateFrom) url += '&date_from=' + dateFrom;
            if (dateTo) url += '&date_to=' + dateTo;
            
            window.location.href = url;
        };

        // Quick date presets for export modal
        window.setExportDate = function(range) {
            const today = new Date();
            let from, to;
            switch(range) {
                case 'today':
                    from = to = today.toISOString().split('T')[0];
                    break;
                case 'week':
                    from = new Date(today.setDate(today.getDate() - 7)).toISOString().split('T')[0];
                    to = new Date().toISOString().split('T')[0];
                    break;
                case 'month':
                    from = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                    to = new Date().toISOString().split('T')[0];
                    break;
                case 'year':
                    from = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
                    to = new Date().toISOString().split('T')[0];
                    break;
                case 'all':
                    from = '2000-01-01';
                    to = new Date().toISOString().split('T')[0];
                    break;
            }
            document.getElementById('exportDateFrom').value = from;
            document.getElementById('exportDateTo').value = to;
        };

        // Confirm export with modal values
        window.confirmExport = function() {
            const dateFrom = document.getElementById('exportDateFrom').value;
            const dateTo = document.getElementById('exportDateTo').value;
            const format = document.getElementById('exportFormat').value;
            
            let url = '{{ route('admin.analytics.export') }}?format=' + format;
            if (dateFrom) url += '&date_from=' + dateFrom;
            if (dateTo) url += '&date_to=' + dateTo;
            
            window.location.href = url;
        };
        } catch(e) {
            console.error('Analytics initialization error:', e);
        }
    });
    </script>
@endpush
</html>