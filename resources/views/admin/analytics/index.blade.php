@extends('layouts.admin')

@section('title', 'Analytics Dashboard')

@section('styles')
<style>
/* NOTE: intentionally left styles as-is */

    .stat-card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .stat-icon { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
    .stat-value { font-size: 2rem; font-weight: 700; color: #1f2937; }
    .stat-label { font-size: 0.875rem; color: #6b7280; }

    /* Executive summary cards */

    /* Make executive cards flexible-width and equal-height */
    /* Executive cards: allow wrapping + ensure each card can occupy 50% width */
    .executive-stats-row > [class*="col"] {
        display: flex;
        padding-left: 0;
        padding-right: 0;
        flex: 0 0 auto;
        width: 100%;
        max-width: 100%;
        margin-left: 0 !important;
        margin-right: 0 !important;
        margin-top: 0 !important;
    }

    .executive-stats-row {
        flex-wrap: wrap;
        row-gap: 16px;
    }

    @media (min-width: 992px) {
        /* 4 columns per row => 2 rows for 8 cards */
        .executive-stats-row > [class*="col"] {
            flex: 0 0 25% !important;
            max-width: 25% !important;
        }
    }






    /* Remove Bootstrap gutters (row g-* introduces visible spacing) */
    .executive-stats-row{
        gap: 0 !important;
        --bs-gutter-x: 0 !important;
        --bs-gutter-y: 0 !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }




    .executive-stats-row .stat-card{
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: stretch;
        padding: 18px;
    }

    .executive-stats-row .stat-card > .d-flex.justify-content-between{
        width: 100%;
        align-items: center;
        gap: 12px;
    }

    .executive-stats-row .stat-card > .d-flex.justify-content-between > div:first-child{
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .executive-stats-row .stat-card > .d-flex.justify-content-between > div:first-child .stat-value{
        line-height: 1.1;
        white-space: normal;
    }

    .executive-stats-row .stat-card > .d-flex.justify-content-between > div:first-child .stat-label{
        font-size: 0.875rem;
        color: #6b7280;
        white-space: normal;
        overflow: visible;
        text-overflow: unset;
        max-width: 100%;
    }

    /* Allow wrapping in executive cards */
    .executive-stats-row .stat-card .stat-label,
    .executive-stats-row .stat-card .stat-value {
        overflow-wrap: anywhere;
        word-break: break-word;
    }





    /* Keep card content fully visible */
    .executive-stats-row .stat-card {
        overflow: visible;
    }

    .executive-stats-row .stat-value {
        overflow: visible;
        text-overflow: unset;
    }

    /* Make executive labels readable on smaller widths */
    @media (max-width: 767.98px) {
        .executive-stats-row .stat-card > .d-flex.justify-content-between > div:first-child .stat-value {
            white-space: normal;
        }
        .executive-stats-row .stat-card > .d-flex.justify-content-between > div:first-child .stat-label {
            white-space: normal;
            overflow: visible;
            text-overflow: unset;
        }
    }

    .growth-badge { font-size: 0.75rem; padding: 4px 10px; border-radius: 999px; font-weight: 600; }
    .growth-up { background: #dcfce7; color: #166534; }
    .growth-down { background: #fee2e2; color: #991b1b; }
    .section-title { font-size: 1.125rem; font-weight: 700; color: #1f2937; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    .chart-card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px; }
    .filter-card { background: white; border-radius: 16px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px; }
    .analytics-filter-actions .btn { flex: 1 1 auto; white-space: nowrap; }
    .analytics-filter-actions .analytics-filter-settings { flex: 0 0 auto; }
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
            <form method="GET" action="/admin/analytics">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">General Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Search all analytics..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Quick Dates</label>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('today')">Today</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setDateRange('week')">Week</button>
                        </div>
                    </div>
                    <!-- Actions --> <div class="col-md-3 d-flex align-items-end"> <div class="d-flex gap-2 w-100"> <!-- Apply --> <button type="submit" class="btn btn-primary" > <i class="bi bi-funnel me-1"></i> Apply </button> <!-- Advanced Filters --> <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#advancedFilters" aria-label="Configure filters" title="Configure filters" > <i class="bi bi-sliders"></i> </button> <!-- Clear All --> <button type="button" class="btn btn-outline-danger" onclick="clearAllFilters()" > <i class="bi bi-x-circle me-1"></i> Clear All </button> </div> </div>
                </div>

            <div class="collapse {{ count(request()->except(['date_from', 'date_to', 'search'])) > 0 ? 'show' : '' }} mt-1" id="advancedFilters">
                <hr>
                <div class="accordion accordion-flush" id="filterAccordion">
                    <!-- Resident & Demographics -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#filterResident">
                                <i class="bi bi-people me-2"></i>Resident Demographics
                            </button>
                        </h2>
                        <div id="filterResident" class="accordion-collapse collapse" data-bs-parent="#filterAccordion">
                            <div class="accordion-body row g-3">
                                <div class="col-md-3">
                                    <label class="form-label small">Account Number</label>
                                    <input type="text" name="account_no" class="form-control form-control-sm" placeholder="ACC-XXXX" value="{{ request('account_no') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Resident Name</label>
                                    <input type="text" name="resident_name" class="form-control form-control-sm" placeholder="First or Last Name" value="{{ request('resident_name') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Address</label>
                                    <input type="text" name="address_line" class="form-control form-control-sm" placeholder="Address" value="{{ request('address_line') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">Sex</label>
                                    <select name="sex" class="form-select form-select-sm">
                                        <option value="">All</option>
                                        <option value="male" {{ request('sex') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ request('sex') == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">Civil Status</label>
                                    <select name="civil_status" class="form-select form-select-sm">
                                        <option value="">All</option>
                                        <option value="single" {{ request('civil_status') == 'single' ? 'selected' : '' }}>Single</option>
                                        <option value="married" {{ request('civil_status') == 'married' ? 'selected' : '' }}>Married</option>
                                        <option value="widowed" {{ request('civil_status') == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                        <option value="separated" {{ request('civil_status') == 'separated' ? 'selected' : '' }}>Separated</option>
                                        <option value="divorced" {{ request('civil_status') == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">Phase</label>
                                    <select name="phase" class="form-select form-select-sm">
                                        <option value="">All</option>
                                        <option value="A" {{ request('phase') == 'A' ? 'selected' : '' }}>A</option>
                                        <option value="B" {{ request('phase') == 'B' ? 'selected' : '' }}>B</option>
                                        <option value="C" {{ request('phase') == 'C' ? 'selected' : '' }}>C</option>
                                        <option value="no_phase" {{ request('phase') == 'no_phase' ? 'selected' : '' }}>No Phase</option>
                                    </select>
                                </div>


                                <div class="col-md-2">
                                    <label class="form-label small">Min Age</label>
                                    <input type="number" name="min_age" class="form-control form-control-sm" value="{{ request('min_age') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">Max Age</label>
                                    <input type="number" name="max_age" class="form-control form-control-sm" value="{{ request('max_age') }}">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label small">Occupation</label>
                                    <select name="occupation" class="form-select form-select-sm">
                                        <option value="">All</option>
                                        <option value="student" {{ request('occupation') == 'student' ? 'selected' : '' }}>Student</option>
                                        <option value="employed" {{ request('occupation') == 'employed' ? 'selected' : '' }}>Employed (Regular)</option>
                                        <option value="contractual" {{ request('occupation') == 'contractual' ? 'selected' : '' }}>Contractual/Temporary</option>
                                        <option value="self_employed" {{ request('occupation') == 'self_employed' ? 'selected' : '' }}>Self-employed</option>
                                        <option value="business_owner" {{ request('occupation') == 'business_owner' ? 'selected' : '' }}>Business Owner</option>
                                        <option value="farmer" {{ request('occupation') == 'farmer' ? 'selected' : '' }}>Farmer</option>
                                        <option value="fisherman" {{ request('occupation') == 'fisherman' ? 'selected' : '' }}>Fisherman</option>
                                        <option value="vendor" {{ request('occupation') == 'vendor' ? 'selected' : '' }}>Vendor/Trader</option>
                                        <option value="artisan" {{ request('occupation') == 'artisan' ? 'selected' : '' }}>Artisan/Craftsman</option>
                                        <option value="driver" {{ request('occupation') == 'driver' ? 'selected' : '' }}>Driver</option>
                                        <option value="construction_worker" {{ request('occupation') == 'construction_worker' ? 'selected' : '' }}>Construction Worker</option>
                                        <option value="laborer" {{ request('occupation') == 'laborer' ? 'selected' : '' }}>General Laborer</option>
                                        <option value="domestic_worker" {{ request('occupation') == 'domestic_worker' ? 'selected' : '' }}>Domestic Worker</option>
                                        <option value="healthcare" {{ request('occupation') == 'healthcare' ? 'selected' : '' }}>Healthcare Worker</option>
                                        <option value="teacher" {{ request('occupation') == 'teacher' ? 'selected' : '' }}>Teacher</option>
                                        <option value="engineer" {{ request('occupation') == 'engineer' ? 'selected' : '' }}>Engineer</option>
                                        <option value="accountant" {{ request('occupation') == 'accountant' ? 'selected' : '' }}>Accountant</option>
                                        <option value="manager" {{ request('occupation') == 'manager' ? 'selected' : '' }}>Manager/Supervisor</option>
                                        <option value="administrative" {{ request('occupation') == 'administrative' ? 'selected' : '' }}>Administrative/Clerical</option>
                                        <option value="sales" {{ request('occupation') == 'sales' ? 'selected' : '' }}>Sales Representative</option>
                                        <option value="service" {{ request('occupation') == 'service' ? 'selected' : '' }}>Service Industry</option>
                                        <option value="hospitality" {{ request('occupation') == 'hospitality' ? 'selected' : '' }}>Hospitality/Tourism</option>
                                        <option value="transport" {{ request('occupation') == 'transport' ? 'selected' : '' }}>Transportation</option>
                                        <option value="manufacturing" {{ request('occupation') == 'manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                                        <option value="it_professional" {{ request('occupation') == 'it_professional' ? 'selected' : '' }}>IT Professional</option>
                                        <option value="creative" {{ request('occupation') == 'creative' ? 'selected' : '' }}>Creative Professional</option>
                                        <option value="ofw" {{ request('occupation') == 'ofw' ? 'selected' : '' }}>Overseas Filipino Worker (OFW)</option>
                                        <option value="retired" {{ request('occupation') == 'retired' ? 'selected' : '' }}>Retired</option>
                                        <option value="unemployed" {{ request('occupation') == 'unemployed' ? 'selected' : '' }}>Unemployed</option>
                                        <option value="pwswd" {{ request('occupation') == 'pwswd' ? 'selected' : '' }}>PWD/Senior Citizen (Unable to Work)</option>
                                        <option value="homemaker" {{ request('occupation') == 'homemaker' ? 'selected' : '' }}>Homemaker</option>
                                        <option value="other" {{ request('occupation') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Educational Attainment</label>
                                    <select name="educational_attainment" class="form-select form-select-sm">
                                        <option value="">All</option>
                                        <option value="no_formal" {{ request('educational_attainment') == 'no_formal' ? 'selected' : '' }}>No Formal Education</option>
                                        <option value="elementary" {{ request('educational_attainment') == 'elementary' ? 'selected' : '' }}>Elementary</option>
                                        <option value="junior_high" {{ request('educational_attainment') == 'junior_high' ? 'selected' : '' }}>Junior High School</option>
                                        <option value="senior_high" {{ request('educational_attainment') == 'senior_high' ? 'selected' : '' }}>Senior High School (SHS)</option>
                                        <option value="vocational" {{ request('educational_attainment') == 'vocational' ? 'selected' : '' }}>Vocational/Technical</option>
                                        <option value="college" {{ request('educational_attainment') == 'college' ? 'selected' : '' }}>College/University</option>
                                        <option value="postgraduate" {{ request('educational_attainment') == 'postgraduate' ? 'selected' : '' }}>Postgraduate</option>
                                    </select>
                                </div>



                                <div class="col-md-12">
                                    <label class="form-label small">Classifications</label>
                                    <div class="d-flex flex-wrap gap-2">

                                        <div class="form-check small">
                                            <input class="form-check-input" type="checkbox" name="is_pwd" value="1" id="f_pwd" {{ request('is_pwd') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="f_pwd">PWD</label>
                                        </div>
                                        <div class="form-check small">
                                            <input class="form-check-input" type="checkbox" name="is_senior" value="1" id="f_senior" {{ request('is_senior') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="f_senior">Senior</label>
                                        </div>
                                        <div class="form-check small">
                                            <input class="form-check-input" type="checkbox" name="is_indigent" value="1" id="f_indigent" {{ request('is_indigent') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="f_indigent">Indigent</label>
                                        </div>
                                        <div class="form-check small">
                                            <input class="form-check-input" type="checkbox" name="is_4ps" value="1" id="f_4ps" {{ request('is_4ps') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="f_4ps">4Ps</label>
                                        </div>
                                        <div class="form-check small">
                                            <input class="form-check-input" type="checkbox" name="is_solo_parent" value="1" id="f_solo" {{ request('is_solo_parent') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="f_solo">Solo Parent</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Household & Housing -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#filterHousehold">
                                <i class="bi bi-house me-2"></i>Household & Socio-Economic
                            </button>
                        </h2>
                        <div id="filterHousehold" class="accordion-collapse collapse" data-bs-parent="#filterAccordion">
                            <div class="accordion-body row g-3">
                                <div class="col-md-3">
                                    <label class="form-label small">Income Range</label>
                                    <select name="income_range" class="form-select form-select-sm">
                                        <option value="">All Income</option>
                                        <option value="Below 10,000" {{ request('income_range') == 'Below 10,000' ? 'selected' : '' }}>Below ₱10,000</option>
                                        <option value="10,000 - 30,000" {{ request('income_range') == '10,000 - 30,000' ? 'selected' : '' }}>₱10,000 - ₱30,000</option>
                                        <option value="30,000 - 50,000" {{ request('income_range') == '30,000 - 50,000' ? 'selected' : '' }}>₱30,000 - ₱50,000</option>
                                        <option value="Above 50,000" {{ request('income_range') == 'Above 50,000' ? 'selected' : '' }}>Above ₱50,000</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Home Ownership</label>
                                    <select name="homeownership_type" class="form-select form-select-sm">
                                        <option value="">All Types</option>
                                        <option value="Owned" {{ request('homeownership_type') == 'Owned' ? 'selected' : '' }}>Owned</option>
                                        <option value="Rented" {{ request('homeownership_type') == 'Rented' ? 'selected' : '' }}>Rented</option>
                                        <option value="Informal Settler" {{ request('homeownership_type') == 'Informal Settler' ? 'selected' : '' }}>Informal Settler</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Household Phase</label>
                                    <select name="household_phase" class="form-select form-select-sm">
                                        <option value="">All</option>
                                        <option value="A" {{ request('household_phase') == 'A' ? 'selected' : '' }}>A</option>
                                        <option value="B" {{ request('household_phase') == 'B' ? 'selected' : '' }}>B</option>
                                        <option value="C" {{ request('household_phase') == 'C' ? 'selected' : '' }}>C</option>
                                        <option value="no_phase" {{ request('household_phase') == 'no_phase' ? 'selected' : '' }}>No Phase</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Household Indicators (Residents)</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        <div class="form-check small">
                                            <input class="form-check-input" type="checkbox" name="is_pwd" value="1" id="f_house_pwd" {{ request('is_pwd') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="f_house_pwd">PWD</label>
                                        </div>
                                        <div class="form-check small">
                                            <input class="form-check-input" type="checkbox" name="is_senior" value="1" id="f_house_senior" {{ request('is_senior') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="f_house_senior">Senior</label>
                                        </div>
                                        <div class="form-check small">
                                            <input class="form-check-input" type="checkbox" name="is_indigent" value="1" id="f_house_indigent" {{ request('is_indigent') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="f_house_indigent">Indigent</label>
                                        </div>
                                        <div class="form-check small">
                                            <input class="form-check-input" type="checkbox" name="is_4ps" value="1" id="f_house_4ps" {{ request('is_4ps') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="f_house_4ps">4Ps</label>
                                        </div>
                                        <div class="form-check small">
                                            <input class="form-check-input" type="checkbox" name="is_solo_parent" value="1" id="f_house_solo_parent" {{ request('is_solo_parent') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="f_house_solo_parent">Solo Parent</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small">Utilities & Facilities</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        <div class="form-check small">
                                            <input class="form-check-input" type="checkbox" name="has_electricity" value="1" id="f_elec" {{ request('has_electricity') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="f_elec">Electricity</label>
                                        </div>
                                        <div class="form-check small">
                                            <input class="form-check-input" type="checkbox" name="has_water" value="1" id="f_water" {{ request('has_water') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="f_water">Water</label>
                                        </div>
                                        <div class="form-check small">
                                            <input class="form-check-input" type="checkbox" name="has_toilet" value="1" id="f_toilet" {{ request('has_toilet') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="f_toilet">Toilet</label>
                                        </div>
                                        <div class="form-check small">
                                            <input class="form-check-input" type="checkbox" name="has_garage" value="1" id="f_garage" {{ request('has_garage') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="f_garage">Garage</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small">Barangay Program Participation</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @php
                                            $programs = [
                                                'Clean-up drives',
                                                'Health programs',
                                                'Livelihood training',
                                                'Scholarship assistance',
                                                'Disaster preparedness',
                                                // Additional suggested programs (kept non-null to match household form behavior)
                                                'Feeding program',
                                                'Vaccination program'
                                            ];
                                        @endphp
                                        @php
                                            $selectedPrograms = (array) request('barangay_program_participation');
                                            $knownProgramsSet = array_flip($programs);
                                        @endphp

                                        @foreach($programs as $program)
                                            <div class="form-check small">
                                                <input class="form-check-input" type="checkbox" name="barangay_program_participation[]" value="{{ $program }}" id="f_prog_{{ str_replace([' ', '&', '-'], '_', strtolower($program)) }}" {{ in_array($program, $selectedPrograms, true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="f_prog_{{ str_replace([' ', '&', '-'], '_', strtolower($program)) }}">{{ $program }}</label>
                                            </div>
                                        @endforeach

                                        <div class="form-check small mt-1">
                                            <input class="form-check-input" type="checkbox" name="barangay_program_participation_other" value="1" id="f_prog_other" {{ request('barangay_program_participation_other') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="f_prog_other">Other</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Services & Payments -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#filterServices">
                                <i class="bi bi-file-earmark-text me-2"></i>Services & Revenue
                            </button>
                        </h2>
                        <div id="filterServices" class="accordion-collapse collapse" data-bs-parent="#filterAccordion">
                            <div class="accordion-body row g-3">


                                <div class="col-md-3">
                                    <label class="form-label small">Document Status</label>
                                    <select name="doc_status" class="form-select form-select-sm">
                                        <option value="">All Status</option>
                                        <option value="pending" {{ request('doc_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ request('doc_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="released" {{ request('doc_status') == 'released' ? 'selected' : '' }}>Released</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label small">Payment Status</label>
                                    <select name="payment_status" class="form-select form-select-sm">
                                        <option value="">All Payments</option>
                                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Document Type</label>
                                    <select name="document_type_id" class="form-select form-select-sm">
                                        <option value="">All Document Types</option>
                                        @foreach(($documentTypes ?? []) as $dt)
                                            <option value="{{ $dt->id }}" {{ request('document_type_id') == $dt->id ? 'selected' : '' }}>
                                                {{ $dt->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Min Amount</label>
                                    <input type="number" name="min_amount" class="form-control form-control-sm" value="{{ request('min_amount') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Max Amount</label>
                                    <input type="number" name="max_amount" class="form-control form-control-sm" value="{{ request('max_amount') }}">
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Peace & Order -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed py-2" type="button" data-bs-toggle="collapse" data-bs-target="#filterSecurity">
                                <i class="bi bi-shield-check me-2"></i>Peace & Order
                            </button>
                        </h2>
                        <div id="filterSecurity" class="accordion-collapse collapse" data-bs-parent="#filterAccordion">
                            <div class="accordion-body row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small">Incident Type</label>
                                    <select name="incident_type" class="form-select form-select-sm">
                                        <option value="" {{ request('incident_type') ? '' : 'selected' }}>All Incident Types</option>


                                        <optgroup label="Personal Offenses">
                                            <option value="Threats / Harassment" {{ request('incident_type') == 'Threats / Harassment' ? 'selected' : '' }}>⚠️ Threats / Harassment</option>
                                            <option value="Physical Injury / Assault" {{ request('incident_type') == 'Physical Injury / Assault' ? 'selected' : '' }}>👊 Physical Injury / Assault</option>
                                            <option value="Domestic Dispute" {{ request('incident_type') == 'Domestic Dispute' ? 'selected' : '' }}>🏠 Domestic Dispute</option>
                                            <option value="Verbal Altercation" {{ request('incident_type') == 'Verbal Altercation' ? 'selected' : '' }}>🗣️ Verbal Altercation</option>
                                            <option value="Child Abuse" {{ request('incident_type') == 'Child Abuse' ? 'selected' : '' }}>👶 Child Abuse</option>
                                            <option value="Violence Against Women" {{ request('incident_type') == 'Violence Against Women' ? 'selected' : '' }}>👩 Violence Against Women</option>
                                        </optgroup>

                                        <optgroup label="Property Crimes">
                                            <option value="Theft / Robbery" {{ request('incident_type') == 'Theft / Robbery' ? 'selected' : '' }}>💰 Theft / Robbery</option>
                                            <option value="Property Damage" {{ request('incident_type') == 'Property Damage' ? 'selected' : '' }}>🏗️ Property Damage</option>
                                            <option value="Trespassing" {{ request('incident_type') == 'Trespassing' ? 'selected' : '' }}>🚫 Trespassing</option>
                                        </optgroup>

                                        <optgroup label="Public Nuisance">
                                            <option value="Noise Disturbance" {{ request('incident_type') == 'Noise Disturbance' ? 'selected' : '' }}>🔊 Noise Disturbance</option>
                                            <option value="Illegal Gambling" {{ request('incident_type') == 'Illegal Gambling' ? 'selected' : '' }}>🎰 Illegal Gambling</option>
                                            <option value="Drunk in Public" {{ request('incident_type') == 'Drunk in Public' ? 'selected' : '' }}>🍺 Drunk in Public</option>
                                            <option value="Stray Animal" {{ request('incident_type') == 'Stray Animal' ? 'selected' : '' }}>🐕 Stray Animal</option>
                                            <option value="Public Hazard" {{ request('incident_type') == 'Public Hazard' ? 'selected' : '' }}>⚡ Public Hazard</option>
                                        </optgroup>

                                        <optgroup label="Disputes">
                                            <option value="Traffic Accident" {{ request('incident_type') == 'Traffic Accident' ? 'selected' : '' }}>🚗 Traffic Accident</option>
                                            <option value="Land / Boundary Dispute" {{ request('incident_type') == 'Land / Boundary Dispute' ? 'selected' : '' }}>🏞️ Land / Boundary Dispute</option>
                                            <option value="Scam / Estafa" {{ request('incident_type') == 'Scam / Estafa' ? 'selected' : '' }}>💳 Scam / Estafa</option>
                                        </optgroup>

                                        <optgroup label="Environmental">
                                            <option value="Illegal Logging" {{ request('incident_type') == 'Illegal Logging' ? 'selected' : '' }}>🌲 Illegal Logging</option>
                                            <option value="Illegal Fishing" {{ request('incident_type') == 'Illegal Fishing' ? 'selected' : '' }}>🎣 Illegal Fishing</option>
                                            <option value="Water Pollution" {{ request('incident_type') == 'Water Pollution' ? 'selected' : '' }}>💧 Water Pollution</option>
                                            <option value="Illegal Dumping" {{ request('incident_type') == 'Illegal Dumping' ? 'selected' : '' }}>🗑️ Illegal Dumping</option>
                                            <option value="Unsanitary Premises" {{ request('incident_type') == 'Unsanitary Premises' ? 'selected' : '' }}>🧹 Unsanitary Premises</option>
                                        </optgroup>

                                        <optgroup label="Health & Safety">
                                            <option value="Animal Bite" {{ request('incident_type') == 'Animal Bite' ? 'selected' : '' }}>🦇 Animal Bite</option>
                                            <option value="Health Code Violation" {{ request('incident_type') == 'Health Code Violation' ? 'selected' : '' }}>🏥 Health Code Violation</option>
                                            <option value="Business Permit Violation" {{ request('incident_type') == 'Business Permit Violation' ? 'selected' : '' }}>📜 Business Permit Violation</option>
                                        </optgroup>

                                        <optgroup label="Other">
                                            <option value="Cybercrime" {{ request('incident_type') == 'Cybercrime' ? 'selected' : '' }}>💻 Cybercrime</option>
                                            <option value="Squatters / Illegal Settlers" {{ request('incident_type') == 'Squatters / Illegal Settlers' ? 'selected' : '' }}>🏚️ Squatters / Illegal Settlers</option>
                                            <option value="Missing Person" {{ request('incident_type') == 'Missing Person' ? 'selected' : '' }}>🔍 Missing Person</option>
                                            <option value="Found Property" {{ request('incident_type') == 'Found Property' ? 'selected' : '' }}>📦 Found Property</option>
                                            <option value="Other" {{ request('incident_type') == 'Other' ? 'selected' : '' }}>❓ Other</option>
                                        </optgroup>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Case Status</label>
                                    <select name="case_status" class="form-select form-select-sm">
                                        <option value="">All Cases</option>
                                        <option value="ongoing" {{ request('case_status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                        <option value="scheduled" {{ request('case_status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                        <option value="settled" {{ request('case_status') == 'settled' ? 'selected' : '' }}>Settled</option>
                                        <option value="referred" {{ request('case_status') == 'referred' ? 'selected' : '' }}>Certified to File Action</option>
                                        <option value="dismissed" {{ request('case_status') == 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                                        <option value="archived" {{ request('case_status') == 'archived' ? 'selected' : '' }}>Archived</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Hearing Location</label>
                                    <input type="text" name="hearing_loc" class="form-control form-control-sm" placeholder="Location name..." value="{{ request('hearing_loc') }}">
                                </div>
                            </div>
                        </div>
                    </div>
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
                        <!-- Report Type is no longer selectable for Export Analytics; export matches dashboard chart filters -->
                        <input type="hidden" id="exportReportType" value="demographic">

                        <!-- Date range inputs are no longer selectable for Export Analytics (use dashboard filter date_from/date_to). -->
                        <input type="hidden" id="exportDateFrom" value="{{ $dateFrom }}">
                        <input type="hidden" id="exportDateTo" value="{{ $dateTo }}">

                        <div class="mb-3">
                            <label class="form-label">Export Sections</label>
                            <div class="d-flex flex-wrap gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="export_sections[]" value="executive" id="exp_sec_executive" checked>
                                    <label class="form-check-label" for="exp_sec_executive">Executive Dashboard</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="export_sections[]" value="residents" id="exp_sec_residents" checked>
                                    <label class="form-check-label" for="exp_sec_residents">Residents</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="export_sections[]" value="households" id="exp_sec_households" checked>
                                    <label class="form-check-label" for="exp_sec_households">Households</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="export_sections[]" value="documents" id="exp_sec_documents" checked>
                                    <label class="form-check-label" for="exp_sec_documents">Documents</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="export_sections[]" value="payments" id="exp_sec_payments" checked>
                                    <label class="form-check-label" for="exp_sec_payments">Payments</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="export_sections[]" value="blotters" id="exp_sec_blotters" checked>
                                    <label class="form-check-label" for="exp_sec_blotters">Blotters</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="export_sections[]" value="cases" id="exp_sec_cases" checked>
                                    <label class="form-check-label" for="exp_sec_cases">Cases</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="export_sections[]" value="officials" id="exp_sec_officials" checked>
                                    <label class="form-check-label" for="exp_sec_officials">Officials</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="export_sections[]" value="announcements-events" id="exp_sec_announcements_events" checked>
                                    <label class="form-check-label" for="exp_sec_announcements_events">Announcements &amp; Events</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="export_sections[]" value="users-audit" id="exp_sec_users_audit" checked>
                                    <label class="form-check-label" for="exp_sec_users_audit">Users &amp; Audit</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Export Format</label>
                            <select id="exportFormat" class="form-select">
                                <option value="pdf">PDF (with charts)</option>
                                <option value="pdf_charts_table_lists">PDF (with charts + table lists)</option>
                                <option value="csv" hidden>CSV (data only)</option>
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


        <!-- Analytics Tabs -->

        <!-- Executive Summary cards are shown inside the Executive Dashboard tab -->
        <ul class="nav nav-tabs mb-4" id="analyticsTabs" role="tablist">
            @if($permissions['viewExecutiveDashboard'])
            <li class="nav-item" role="presentation">
                <button class="tab-btn active" id="executive-tab" data-bs-toggle="tab" data-bs-target="#executive" type="button" data-hash="#executive">
                    <i class="bi bi-speedometer2 me-2"></i>Executive Dashboard
                </button>
            </li>
            @endif
            @if($permissions['viewResidentAnalytics'])
            <li class="nav-item" role="presentation">
                <button class="tab-btn {{ !$permissions['viewExecutiveDashboard'] ? 'active' : '' }}" id="residents-tab" data-bs-toggle="tab" data-bs-target="#residents" type="button" data-hash="#residents">
                    <i class="bi bi-people me-2"></i>Residents
                </button>
            </li>
            @endif
            @if($permissions['viewHouseholdAnalytics'])
            <li class="nav-item" role="presentation">
                <button class="tab-btn" id="households-tab" data-bs-toggle="tab" data-bs-target="#households" type="button" data-hash="#households">
                    <i class="bi bi-house me-2"></i>Households
                </button>
            </li>
            @endif
            @if($permissions['viewDocumentRequestAnalytics'])
            <li class="nav-item" role="presentation">
                <button class="tab-btn" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" data-hash="#documents">
                    <i class="bi bi-file-earmark me-2"></i>Documents
                </button>
            </li>
            @endif
            @if($permissions['viewPaymentAnalytics'])
            <li class="nav-item" role="presentation">
                <button class="tab-btn" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" data-hash="#payments">
                    <i class="bi bi-cash-coin me-2"></i>Payments
                </button>
            </li>
            @endif
            @if($permissions['viewBlotterAnalytics'])
            <li class="nav-item" role="presentation">
                <button class="tab-btn" id="blotter-tab" data-bs-toggle="tab" data-bs-target="#blotter" type="button" data-hash="#blotter">
                    <i class="bi bi-exclamation-triangle me-2"></i>Blotters
                </button>
            </li>
            @endif
            @if($permissions['viewCaseAnalytics'])
            <li class="nav-item" role="presentation">
                <button class="tab-btn" id="cases-tab" data-bs-toggle="tab" data-bs-target="#cases" type="button" data-hash="#cases">
                    <i class="bi bi-briefcase me-2"></i>Cases
                </button>
            </li>
            @endif
            @if($permissions['viewOfficialAnalytics'])
            <li class="nav-item" role="presentation">
                <button class="tab-btn" id="officials-tab" data-bs-toggle="tab" data-bs-target="#officials" type="button" data-hash="#officials">
                    <i class="bi bi-person-badge me-2"></i>Officials
                </button>
            </li>
            @endif
            @if($permissions['viewAnnouncementEventAnalytics'])
            <li class="nav-item" role="presentation">
                <button class="tab-btn" id="announcements-events-tab" data-bs-toggle="tab" data-bs-target="#announcements-events" type="button" data-hash="#announcements-events">
                    <i class="bi bi-megaphone me-2"></i>Announcements & Events
                </button>
            </li>
            @endif
            @if($permissions['viewAdminSystemAnalytics'])
            <li class="nav-item" role="presentation">
                <button class="tab-btn" id="users-audit-tab" data-bs-toggle="tab" data-bs-target="#users-audit" type="button" data-hash="#users-audit">
                    <i class="bi bi-person-gear me-2"></i>Users & Audit
                </button>
            </li>
            @endif
        </ul>

        {{-- Hash-based tab persistence (script must be outside tab-content to avoid parser issues) --}}
        @push('scripts')
            <script>
                (function () {
                    function activateFromHash() {
                        var hash = (window.location.hash || '').replace('#', '');
                        if (!hash) return;

                        var btn = document.querySelector('#analyticsTabs button[data-hash="#' + hash + '"]');
                        if (!btn) return;

                        if (window.bootstrap && window.bootstrap.Tab) {
                            new window.bootstrap.Tab(btn).show();
                        }
                    }

                    document.addEventListener('click', function (e) {
                        var btn = e.target.closest('#analyticsTabs button[data-hash]');
                        if (!btn) return;

                        var h = btn.getAttribute('data-hash');
                        if (h) window.location.hash = h;
                    });

                    document.addEventListener('DOMContentLoaded', activateFromHash);
                    window.addEventListener('hashchange', activateFromHash);
                })();
            </script>
        @endpush

        <div class="tab-content" id="analyticsTabsContent">








            <!-- Executive Dashboard -->
            @if($permissions['viewExecutiveDashboard'])
            <div class="tab-pane fade show active" id="executive" role="tabpanel">
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="chart-card">
        <h6 class="section-title"><i class="bi bi-graph-up"></i>Executive Summary</h6>
            <p>This section provides a high-level overview for Barangay Captain and Secretary.</p>
                            <!-- Placeholder for executive dashboard content -->
                            <div class="row executive-stats-row justify-content-between align-items-stretch">




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
                                                    {{ $residentGrowth > 0 ? '+' : '' }}{{ $residentGrowth }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

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

                                <div class="col-md-2 col-sm-4 col-6">
                                    <div class="stat-card h-100">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="stat-value">{{ '₱' . number_format($paymentAnalytics['totalCollections'] ?? 0) }}</div>
                                                <div class="stat-label">Total Collections</div>
                                            </div>
                                            <div class="stat-icon" style="background: #d1fae5; color: #065f46;">
                                                <i class="bi bi-cash-coin"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2 col-sm-4 col-6">
                                    <div class="stat-card h-100">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="stat-value">{{ number_format($paymentAnalytics['paidTransactions'] ?? 0) }}</div>
                                                <div class="stat-label">Paid Transactions</div>
                                            </div>
                                            <div class="stat-icon" style="background: #ccfbf1; color: #134e4a;">
                                                <i class="bi bi-check-circle"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Residents Analytics -->
            @if($permissions['viewResidentAnalytics'])
            <div class="tab-pane fade {{ !$permissions['viewExecutiveDashboard'] ? 'show active' : '' }}" id="residents" role="tabpanel">
                <div class="row g-3">
                    <!-- Resident Metrics Quick Stats -->
                    <div class="col-md-12">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-people"></i>Resident Analytics Summary</h6>
                            <div class="row g-3">
                                <div class="col-md-2 col-sm-4">
                                    <div class="stat-card text-center p-3 h-100 border-top border-primary border-4">
                                        <div class="stat-value" style="font-size: 1.5rem;">{{ number_format($totalResidents) }}</div>
                                        <div class="stat-label small">Total Residents</div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-4">
                                    <div class="stat-card text-center p-3 h-100">
                                        <div class="stat-value small d-flex flex-column align-items-center">
                                            <span style="color: #1055C9;"><i class="bi bi-gender-male me-1"></i>{{ number_format($genderStats['male'] ?? 0) }}</span>
                                            <span style="color: #EC4899;"><i class="bi bi-gender-female me-1"></i>{{ number_format($genderStats['female'] ?? 0) }}</span>
                                        </div>
                                        <div class="stat-label small">Male / Female</div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-4">
                                    <div class="stat-card text-center p-3 h-100">
                                        <div class="stat-value" style="font-size: 1.5rem;">{{ number_format($minorPopulation ?? 0) }}</div>
                                        <div class="stat-label small">Minors (<18)</div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-4">
                                    <div class="stat-card text-center p-3 h-100">
                                        <div class="stat-value" style="font-size: 1.5rem;">{{ number_format($adultPopulation ?? 0) }}</div>
                                        <div class="stat-label small">Adults (18-64)</div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-4">
                                    <div class="stat-card text-center p-3 h-100">
                                        <div class="stat-value" style="font-size: 1.5rem;">{{ number_format($seniorCitizens ?? 0) }}</div>
<div class="stat-label small">Seniors (60+)</div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-sm-4">
                                    <div class="stat-card text-center p-3 h-100">
                                        <div class="stat-value" style="font-size: 1.5rem;">{{ $averageAge ?? 0 }}</div>
                                        <div class="stat-label small">Average Age</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3 mt-1">
                                <div class="col-md-3 col-sm-6">
                                    <div class="stat-card d-flex align-items-center gap-3 p-3 h-100">
                                        <div class="stat-icon bg-info bg-opacity-10 text-info" style="width: 40px; height: 40px; font-size: 1.2rem;">
                                            <i class="bi bi-person-wheelchair"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ number_format($pwdResidents ?? 0) }}</div>
                                            <div class="text-muted small">PWD</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="stat-card d-flex align-items-center gap-3 p-3 h-100">
                                        <div class="stat-icon bg-warning bg-opacity-10 text-warning" style="width: 40px; height: 40px; font-size: 1.2rem;">
                                            <i class="bi bi-person-heart"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ number_format($soloParentResidents ?? 0) }}</div>
                                            <div class="text-muted small">Solo Parents</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="stat-card d-flex align-items-center gap-3 p-3 h-100">
                                        <div class="stat-icon bg-danger bg-opacity-10 text-danger" style="width: 40px; height: 40px; font-size: 1.2rem;">
                                            <i class="bi bi-house-heart"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ number_format($indigentResidents ?? 0) }}</div>
                                            <div class="text-muted small">Indigent</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="stat-card d-flex align-items-center gap-3 p-3 h-100">
                                        <div class="stat-icon bg-success bg-opacity-10 text-success" style="width: 40px; height: 40px; font-size: 1.2rem;">
                                            <i class="bi bi-card-checklist"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ number_format($fourPsBeneficiaries ?? 0) }}</div>
                                            <div class="text-muted small">4Ps Beneficiaries</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-person-badge"></i>Gender Distribution</h6>
                            <div id="residentGenderChart"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-bar-chart"></i>Age Groups</h6>
                            <div id="residentAgeChart"></div>
                            
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-shield-lock"></i>Resident Classifications</h6>
                            <div id="residentClassChart"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-geo-alt"></i>Population by Phase</h6>
                            <div id="residentPhaseChart"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-graph-up"></i>Monthly Registration Trends</h6>
                            <div id="residentTrendChart"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-pie-chart"></i>Civil Status Distribution</h6>
                            <div id="civilStatusChart"></div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- New: Household Analytics -->
            @if($permissions['viewHouseholdAnalytics'])
                <!-- Households Analytics -->
                <div class="tab-pane fade" id="households" role="tabpanel" aria-labelledby="households-tab">
                <h6 class="section-title"><i class="bi bi-house"></i>Household Overview</h6>

                <!-- NOTE: Household Analytics charts below -->
                <div class="row g-3">
                     <div class="col-md-4">
                         <div class="stat-card text-center h-100">
                             <div class="stat-value">{{ number_format($totalHouseholds) }}</div>
                             <div class="stat-label">Total Households</div>
                         </div>
                     </div>
                     <div class="col-md-4">
                         <div class="stat-card text-center h-100" >
                             <div class="stat-value">{{ number_format($avgMembers ?? 0, 1) }}</div>
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
                               <h6 class="section-title"><i class="bi bi-house"></i>Homeownership Distribution</h6>
                               <div id="householdTypeChart" aria-label="Homeownership Distribution Chart"></div>
                           </div>
                       </div>
                       <div class="col-md-6">
                           <div class="chart-card">
                               <h6 class="section-title"><i class="bi bi-building"></i>Households by Phase</h6> <!-- Changed from Zone to Phase -->
                           <div id="householdPhaseChart" style="min-height:250px"></div>
                           <div id="debug-householdPhase" style="font-size:12px; color:#c2410c; margin-top:6px;"></div>



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
                      <div class="col-md-12">
                        <h6 class="section-title"><i class="bi bi-graph-up"></i>Monthly Household Trends</h6>
                        <div id="householdTrendChart"></div>
                      </div>

                </div>
            </div>

            <!-- Document Request Analytics -->
            @endif
            @if($permissions['viewDocumentRequestAnalytics'])
            <div class="tab-pane fade" id="documents" role="tabpanel">
                <h6 class="section-title"><i class="bi bi-file-earmark"></i>Document Request Overview</h6>
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
                            <div id="docStatusChart"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-file-earmark-text"></i>Top Document Types</h6>
                            <div id="docTypeChart"></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-graph-up"></i>Monthly Request Trends</h6>
                            <div id="docTrendChart"></div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @if($permissions['viewPaymentAnalytics'])
            <!-- Blotter Analytics -->
            <div class="tab-pane fade" id="payments" role="tabpanel" aria-labelledby="payments-tab">
                <div class="row g-3">
                    <h6 class="section-title"><i class="bi bi-cash-coin"></i>Payment Overview</h6>
                    <div class="col-md-6">
                    <div class="stat-card text-center h-100">
                            <div class="stat-value">{{ number_format($paymentAnalytics['paidTransactions'] ?? 0) }}</div>
                            <div class="stat-label">Paid Transactions</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="stat-card text-center h-100">
                            <div class="stat-value">{{ number_format($paymentAnalytics['unpaidTransactions'] ?? 0) }}</div>
                            <div class="stat-label">Unpaid Transactions</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-pie-chart"></i>Payment Status Distribution</h6>
                            <div id="paymentStatusChart"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-graph-up"></i>Monthly Revenue Trend</h6>
                            <div id="monthlyRevenueChart"></div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($permissions['viewBlotterAnalytics'])
            <div class="tab-pane fade" id="blotter" role="tabpanel">
                <h6 class="section-title"><i class="bi bi-exclamation-triangle"></i>Blotter Overview</h6>
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
                            <div id="caseConversionChart"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-exclamation-triangle"></i>Incident Types</h6>
                            <div id="incidentTypeChart"></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-graph-up"></i>Monthly Incident Trends</h6>
                            <div id="blotterTrendChart"></div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @if($permissions['viewCaseAnalytics'])
            <!-- Case Analytics -->
            <div class="tab-pane fade" id="cases" role="tabpanel">
                <h6 class="section-title"><i class="bi bi-briefcase"></i>Case & Hearing Overview</h6>
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
                            <div id="caseStatusChart"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-briefcase"></i>Case Types</h6>
                            <div id="caseTypeChart"></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-graph-up"></i>Monthly Case Trends</h6>
                            <div id="caseTrendChart"></div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- New: Barangay Officials and Terms Analytics -->
            @if($permissions['viewOfficialAnalytics'])
            <div class="tab-pane fade" id="officials" role="tabpanel" aria-labelledby="officials-tab">
                <h6 class="section-title"><i class="bi bi-person-badge"></i>Barangay Officials Overview</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="stat-card text-center h-100">
                            <div class="stat-value">{{ number_format($totalOfficials ?? 0) }}</div>
                            <div class="stat-label">Total Officials</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card text-center h-100">
                            <div class="stat-value">{{ number_format($activeTerms ?? 0) }}</div>
                            <div class="stat-label">Active Terms</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card text-center h-100">
                            <div class="stat-value">{{ number_format($archivedTerms ?? 0) }}</div>
                            <div class="stat-label">Archived Terms</div>
                        </div>
                    </div>


                    {{-- Active terms officials table(s) --}}
                    @if(!empty($officialsByTerm) && is_array($officialsByTerm) && count($officialsByTerm) > 0)
                        <div class="col-md-12">
                            <div class="chart-card">
                                <h6 class="section-title"><i class="bi bi-table"></i>Active Terms Officials List</h6>

                                @foreach($officialsByTerm as $termLabel => $officialRows)
                                    <div class="mt-3">
                                        <h6 style="font-size:11px;color:#444;margin-bottom:8px;">{{ $termLabel }}</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Position</th>
                                                        <th>Committee</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($officialRows as $row)
                                                        <tr>
                                                            <td>{{ $row['name'] ?? '—' }}</td>
                                                            <td>{{ $row['position'] ?? '—' }}</td>
                                                            <td>{{ $row['committee'] ?? '—' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- New: Announcement and Event Analytics -->
            @if($permissions['viewAnnouncementEventAnalytics'])
            <div class="tab-pane fade" id="announcements-events" role="tabpanel" aria-labelledby="announcements-events-tab">
                <h6 class="section-title"><i class="bi bi-megaphone"></i>Announcements & Events Overview</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="stat-card text-center h-100">
                            <div class="stat-value">{{ number_format($totalAnnouncements ?? 0) }}</div>
                            <div class="stat-label">Total Announcements</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card text-center h-100">
                            <div class="stat-value">{{ number_format($totalEvents ?? 0) }}</div>
                            <div class="stat-label">Total Events</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card text-center h-100">
                            <div class="stat-value">{{ number_format($publishedAnnouncements ?? 0) }}</div>
                            <div class="stat-label">Published Announcements</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-megaphone"></i>Announcement Types</h6>
                            <div id="announcementTypeChart"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-calendar-event"></i>Event Types</h6>
                            <div id="eventTypeChart"></div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- New: User and Administrator Analytics & Audit Logs -->
            @if($permissions['viewAdminSystemAnalytics'])
            <div class="tab-pane fade" id="users-audit" role="tabpanel" aria-labelledby="users-audit-tab">
                <h6 class="section-title"><i class="bi bi-person-gear"></i>Users, Admins & Audit Overview</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="stat-card text-center h-100">
                            <div class="stat-value">{{ number_format($totalUsers ?? 0) }}</div>
                            <div class="stat-label">Total Users</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card text-center h-100">
                            <div class="stat-value">{{ number_format($activeUsers ?? 0) }}</div>
                            <div class="stat-label">Active Users</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card text-center h-100">
                            <div class="stat-value">{{ number_format($inactiveUsers ?? 0) }}</div>
                            <div class="stat-label">Inactive Users</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-diagram-3"></i>User Roles Distribution</h6>
                            <div id="userRoleChart"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-bookmarks"></i>Audit Summary</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="stat-card text-center h-100">
                                        <div class="stat-value">{{ number_format($totalAuditLogs ?? 0) }}</div>
                                        <div class="stat-label">Total Audit Logs</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="stat-card text-center h-100">
                                        <div class="stat-value">{{ number_format($totalAdmins ?? 0) }}</div>
                                        <div class="stat-label">Total Admins</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-gear"></i>Most Modified Modules</h6>
                            <div id="auditModuleChart"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-lightning-charge"></i>Most Common Actions</h6>
                            <div id="auditActionChart"></div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="chart-card">
                            <h6 class="section-title"><i class="bi bi-activity"></i>Top Active Users (by audit activity)</h6>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th class="text-end">Audit Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($mostActiveUsers ?? [] as $row)
                                            <tr>
                                                <td>{{ $row->user->email ?? 'Unknown' }}</td>
                                                <td class="text-end">{{ number_format($row->count ?? 0) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-muted">No audit activity in the selected period.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

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

        

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Analytics: DOM loaded, ApexCharts type:', typeof ApexCharts);

        if (typeof ApexCharts === 'undefined') {
            console.error('ApexCharts is not loaded');
            return;
        }



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

        // Robust Chart Creation Helper using ApexCharts (SVG)
        function createChart(elId, options) {
            const el = document.getElementById(elId);
            if (!el) return null;
            try {
                const chart = new ApexCharts(el, options);
                chart.render();
                return chart;
            } catch (err) {
                console.error(`Failed to create chart ${elId}:`, err);
                return null;
            }
        }

        const commonOptions = {
            chart: { fontFamily: 'Segoe UI, sans-serif', toolbar: { show: false } },
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 }
        };

        // Residents Gender Chart
        const residentGender = createChart('residentGenderChart', {
            ...commonOptions,
            chart: { ...commonOptions.chart, type: 'donut', height: 300 },
            series: [{{ $genderStats['male'] ?? 0 }}, {{ $genderStats['female'] ?? 0 }}],
            labels: ['Male', 'Female'],
            colors: [colors.blue, colors.pink],
            legend: { position: 'bottom' }
        });

        // Resident Classifications Chart
        const residentClass = createChart('residentClassChart', {
            ...commonOptions,
            chart: { ...commonOptions.chart, type: 'bar', height: 300 },
            series: [{
                name: 'Residents',
                data: [{{ $pwdResidents ?? 0 }}, {{ $soloParentResidents ?? 0 }}, {{ $indigentResidents ?? 0 }}, {{ $fourPsBeneficiaries ?? 0 }}]
            }],
            xaxis: { categories: ['PWD', 'Solo Parent', 'Indigent', '4Ps'] },
            colors: [colors.blue],
            plotOptions: { bar: { borderRadius: 4, distributed: true } }
        });

        // Residents Age Chart
        const residentAge = createChart('residentAgeChart', {
            ...commonOptions,
            chart: { ...commonOptions.chart, type: 'bar', height: 300 },
            series: [{
                name: 'Residents',
                data: [{{ $ageGroups['0-17'] ?? 0 }}, {{ $ageGroups['18-30'] ?? 0 }}, {{ $ageGroups['31-50'] ?? 0 }}, {{ $ageGroups['51-65'] ?? 0 }}, {{ $ageGroups['65+'] ?? 0 }}]
            }],
            xaxis: { categories: ['0-17', '18-30', '31-50', '51-65', '65+'] },
            colors: [colors.blue],
            plotOptions: { bar: { borderRadius: 4 } }
        });

        // Residents Trend Chart
        const residentTrend = createChart('residentTrendChart', {
            ...commonOptions,
            chart: { ...commonOptions.chart, type: 'area', height: 300 },
            series: [{
                name: 'Registrations',
                data: fillMonthlyData(@json($residentMonthlyTrends))
            }],
            xaxis: { categories: getMonthLabels() },
            colors: [colors.blue],
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1 } }
        });

        // Civil Status Chart
        const civilStatus = createChart('civilStatusChart', {
            ...commonOptions,
            chart: { ...commonOptions.chart, type: 'pie', height: 300 },
            series: @json(array_values($civilStatusStats ?? [])),
            labels: @json(array_keys($civilStatusStats ?? [])),
            colors: [colors.blue, colors.green, colors.yellow, colors.purple, colors.orange],
            legend: { position: 'bottom' }
        });

        // Household Street Chart (Pie)
        const householdZone = createChart('householdStreetChart', {
            ...commonOptions,
            chart: { ...commonOptions.chart, type: 'pie', height: 300 },
            series: @json(array_values($streetStats)),
            labels: @json(array_keys($streetStats)),
            colors: [colors.green, colors.blue, colors.purple, colors.yellow, colors.orange],
            legend: { position: 'bottom' }
        });


        // Household Type Chart
        const householdType = createChart('householdTypeChart', {
            ...commonOptions,
            chart: { ...commonOptions.chart, type: 'pie', height: 300 },
            series: @json(array_values($homeownershipStats)),
            labels: @json(array_keys($homeownershipStats)),
            colors: [colors.blue, colors.green, colors.yellow],
            legend: { position: 'bottom' },
            title: {
                text: 'Homeownership Distribution',
                align: 'center',
                style: { fontSize: '14px' }
            },
            tooltip: {
                y: {
                    formatter: function(val) { return String(val); }
                }
            },
            dataLabels: { enabled: false }
        });


        // Household Phase Chart (dummy data)
        // DEBUG (remove later): confirm JS executes
        const debugEl = document.getElementById('debug-householdPhase');
        if (debugEl) debugEl.textContent = 'JS executed: creating householdPhaseChart';

        const householdPhase = createChart('householdPhaseChart', {
            ...commonOptions,
            chart: { ...commonOptions.chart, type: 'pie', height: 300 },
            series: @json(array_values($phaseStats ?? [12, 7, 5])),
            labels: @json(array_keys($phaseStats ?? ['A','B','C'])),
            colors: [colors.purple, colors.blue, colors.green, colors.orange, colors.pink],
            legend: { position: 'bottom' }
        });



        // Resident Phase Chart
        const residentPhase = createChart('residentPhaseChart', {

            ...commonOptions,
            chart: { ...commonOptions.chart, type: 'bar', height: 300 },
            series: [{
                name: 'Residents',
                data: @json(array_values($phasePopulation))
            }],
            xaxis: { categories: @json(array_keys($phasePopulation ?? [])) },
            colors: [colors.cyan],
            plotOptions: { bar: { borderRadius: 4 } }
        });

        // Household Trend Chart
        const householdTrend = createChart('householdTrendChart', {
            ...commonOptions,
            chart: { ...commonOptions.chart, type: 'area', height: 300 },
            series: [{
                name: 'New Households',
                data: fillMonthlyData(@json($householdMonthlyTrends))
            }],
            xaxis: { categories: getMonthLabels() },
            colors: [colors.green],
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1 } }
        });

        // Document Status Chart
        const docStatus = createChart('docStatusChart', {
            ...commonOptions,
            chart: { ...commonOptions.chart, type: 'donut', height: 300 },
            series: @json(array_values($docStatusStats)),
            labels: @json(array_keys($docStatusStats)),
            colors: [colors.yellow, colors.green, colors.red, colors.blue],
            legend: { position: 'bottom' }
        });

        // Document Type Chart
        const docType = createChart('docTypeChart', {
            ...commonOptions,
            chart: { ...commonOptions.chart, type: 'bar', height: 300 },
            series: [{
                name: 'Requests',
                data: @json(array_values($topTypes))
            }],
            xaxis: { categories: @json(array_keys($topTypes)) },
            colors: [colors.yellow],
            plotOptions: { bar: { horizontal: true, borderRadius: 4 } }
        });

        // Document Trend Chart
        const docTrend = createChart('docTrendChart', {
            ...commonOptions,
            chart: { ...commonOptions.chart, type: 'area', height: 300 },
            series: [{
                name: 'Requests',
                data: fillMonthlyData(@json($docMonthlyTrends))
            }],
            xaxis: { categories: getMonthLabels() },
            colors: [colors.yellow],
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1 } }
        });

        // Incident Type Chart
        const incidentType = createChart('incidentTypeChart', {
            ...commonOptions,
            chart: { ...commonOptions.chart, type: 'bar', height: 300 },
            series: [{
                name: 'Incidents',
                data: @json(array_values($incidentTypeStats))
            }],
            xaxis: { categories: @json(array_keys($incidentTypeStats)) },
            colors: [colors.red],
            plotOptions: { bar: { horizontal: true, borderRadius: 4 } }
        });

        // Case Conversion Chart
        const caseConversion = createChart('caseConversionChart', {
            ...commonOptions,
            chart: { ...commonOptions.chart, type: 'donut', height: 300 },
            series: [@json($withCase ?? 0), @json($withoutCase ?? 0)],
            labels: ['With Case', 'No Case'],
            colors: [colors.purple, colors.gray],
            legend: { position: 'bottom' }
        });

        // Blotter Trend Chart
        const blotterTrend = createChart('blotterTrendChart', {
            ...commonOptions,
            chart: { ...commonOptions.chart, type: 'area', height: 300 },
            series: [{
                name: 'Incidents',
                data: fillMonthlyData(@json($blotterMonthlyTrends))
            }],
            xaxis: { categories: getMonthLabels() },
            colors: [colors.red],
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1 } }
        });

        // Case Status Chart
        const caseStatus = createChart('caseStatusChart', {
            ...commonOptions,
            chart: { ...commonOptions.chart, type: 'donut', height: 300 },
            series: @json(array_values($caseStatusStats)),
            labels: @json(array_keys($caseStatusStats)),
            colors: [colors.purple, colors.yellow, colors.green, colors.red],
            legend: { position: 'bottom' }
        });

        // Case Type Chart (Pie)
        const caseType = createChart('caseTypeChart', {
            ...commonOptions,
            chart: { ...commonOptions.chart, type: 'pie', height: 300 },
            series: @json(array_values($caseTypeStats)),
            labels: @json(array_keys($caseTypeStats)),
            colors: [colors.purple, colors.blue, colors.green, colors.yellow, colors.orange, colors.red, colors.pink],
            legend: { position: 'bottom' },
            dataLabels: { enabled: false }
        });

        // NOTE: ensure this script block remains valid JS even when Blade variables are empty.
        // If $caseTypeStats is empty, ApexCharts will receive empty series/labels and should no-op gracefully.

        // Case Trend Chart
        const caseTrend = createChart('caseTrendChart', {
            ...commonOptions,
            chart: { ...commonOptions.chart, type: 'area', height: 300 },
            series: [{
                name: 'Cases',
                data: fillMonthlyData(@json($caseMonthlyTrends))
            }],
            xaxis: { categories: getMonthLabels() },
            colors: [colors.purple],
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1 } }
        });

        // Payment Status Chart
        const paymentStatus = createChart('paymentStatusChart', {
            ...commonOptions,
            chart: { ...commonOptions.chart, type: 'pie', height: 300 },
            series: @json(array_values($paymentAnalytics['statusChart'] ?? [])),
            labels: @json(array_keys($paymentAnalytics['statusChart'] ?? [])),
            colors: [colors.green, colors.gray],
            legend: { position: 'bottom' }
        });

        // Monthly Revenue Chart
        const monthlyRevenue = createChart('monthlyRevenueChart', {
            ...commonOptions,
            chart: { ...commonOptions.chart, type: 'area', height: 300 },
            series: [{
                name: 'Revenue',
                data: fillMonthlyData(@json($paymentAnalytics['monthlyRevenue'] ?? []))
            }],

            xaxis: { categories: getMonthLabels() },
            colors: [colors.green],
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.1 } }
        });
       

        // Officials Charts
        const officialPosition = createChart('officialPositionChart', {
            ...commonOptions,
            chart: { ...commonOptions.chart, type: 'bar', height: 300 },
            series: [{
                name: 'Officials',
                data: @json(array_values($positionStats ?? []))
            }],
            xaxis: { categories: @json(array_keys($positionStats ?? [])) },
            colors: [colors.blue],
            plotOptions: { bar: { horizontal: true, borderRadius: 4 } }
        });

        const officialCommittee = createChart('officialCommitteeChart', {
            ...commonOptions,
            chart: { ...commonOptions.chart, type: 'pie', height: 300 },
            series: @json(array_values($committeeStats ?? [])),
            labels: @json(array_keys($committeeStats ?? [])),
            colors: [colors.blue, colors.green, colors.yellow, colors.purple],
            legend: { position: 'bottom' }
        });

        // Announcement & Event Charts
        const announcementType = createChart('announcementTypeChart', {
            ...commonOptions,
            chart: { ...commonOptions.chart, type: 'donut', height: 300 },
            series: @json(array_values($announcementTypes ?? [])),
            labels: @json(array_keys($announcementTypes ?? [])),
            colors: [colors.blue, colors.yellow, colors.red],
            legend: { position: 'bottom' }
        });

        const eventType = createChart('eventTypeChart', {
            ...commonOptions,
            chart: { ...commonOptions.chart, type: 'bar', height: 300 },
            series: [{
                name: 'Events',
                data: @json(array_values($eventTypes ?? []))
            }],
            xaxis: { categories: @json(array_keys($eventTypes ?? [])) },
            colors: [colors.orange],
            plotOptions: { bar: { borderRadius: 4 } }
        });

        // User roles chart
        const userRoleChart = createChart('userRoleChart', {
            ...commonOptions,
            chart: { ...commonOptions.chart, type: 'pie', height: 300 },
            series: @json(array_values($userRoleDistribution ?? [])),
            labels: @json(array_keys($userRoleDistribution ?? [])),
            colors: [colors.blue, colors.green, colors.purple, colors.orange, colors.red],
            legend: { position: 'bottom' }
        });

        // Audit modules chart
        const auditModuleChart = createChart('auditModuleChart', {
            ...commonOptions,
            chart: { ...commonOptions.chart, type: 'bar', height: 300 },
            series: [{
                name: 'Updates',
                data: @json(array_values($mostModifiedModules ?? []))
            }],
            xaxis: { categories: @json(array_keys($mostModifiedModules ?? [])) },
            colors: [colors.blue],
            plotOptions: { bar: { horizontal: true, borderRadius: 4 } }
        });

        // Audit actions chart
        const auditActionChart = createChart('auditActionChart', {
            ...commonOptions,
            chart: { ...commonOptions.chart, type: 'donut', height: 300 },
            series: @json(array_values($mostCommonActions ?? [])),
            labels: @json(array_keys($mostCommonActions ?? [])),
            colors: [colors.purple, colors.gray, colors.orange, colors.red, colors.green],
            legend: { position: 'bottom' }
        });

        // Store all charts for resize on tab switch
        const charts = {
            residentGender,
            residentAge,
            residentClass,
            residentTrend,
            civilStatus,
            householdZone,
            householdTrend,
            docStatus,
            docType,
            docTrend,
            incidentType,
            blotterTrend,
            residentPhase,
            householdType,
            householdPhase,
            caseConversion,
            caseStatus,
            caseType,
            caseTrend,
            paymentStatus,
            monthlyRevenue,
            officialPosition,
            officialCommittee,
            announcementType,
            eventType,
            userRoleChart,
            auditModuleChart,
            auditActionChart
        };

        // Resize charts when tab becomes visible
        const tabButtons = document.querySelectorAll('#analyticsTabs button[data-bs-toggle="tab"]');
        tabButtons.forEach(button => {
            button.addEventListener('shown.bs.tab', event => {
                setTimeout(() => {
                    // If we just switched to the Households tab, render its chart
                    if (button.dataset.bsTarget === '#households') {
                        try {
                            renderHouseholdPhaseChart();
                        } catch (e) {
                            console.error('HouseholdPhaseChart render failed:', e);
                        }
                    }

                    const targetPane = document.querySelector(button.dataset.bsTarget);
                    if (!targetPane) return;

                    Object.values(charts).forEach(c => {
                        try { /* no-op */ } catch (e) {}
                    });
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

        // Clear all filters
        window.clearAllFilters = function() {
            window.location.href = window.location.pathname;
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



        // Confirm export with modal values
        window.confirmExport = function() {
            const dateFrom = document.getElementById('exportDateFrom').value;
            const dateTo = document.getElementById('exportDateTo').value;
            const format = document.getElementById('exportFormat').value;
            const reportType = document.getElementById('exportReportType').value;

            const exportUrl = new URL('{{ route('admin.analytics.export') }}', window.location.origin);
            exportUrl.searchParams.set('format', format);
            exportUrl.searchParams.set('report_type', reportType);
            if (dateFrom) exportUrl.searchParams.set('date_from', dateFrom);
            if (dateTo) exportUrl.searchParams.set('date_to', dateTo);

            // Copy current analytics filter values (so export charts match dashboard filters)
            const filterForm = document.querySelector('.filter-card form');
            if (filterForm) {
                const formData = new FormData(filterForm);
                // Append multi-value params (e.g. barangay_program_participation[])
                for (const [key, value] of formData.entries()) {
                    if (value === '' || value === null || typeof value === 'undefined') continue;
                    // For checkboxes, only checked ones appear in FormData
                    exportUrl.searchParams.append(key, value);
                }
            }

            // Append selected export sections (checkboxes in the modal)
            const exportSections = Array.from(document.querySelectorAll('input[name="export_sections[]"]:checked'))
                .map(cb => cb.value);
            if (exportSections.length) {
                exportSections.forEach(section => exportUrl.searchParams.append('export_sections[]', section));
            }

            window.location.href = exportUrl.toString();
        };

        // Preserve current analytics tab hash when submitting the filter form
        const filterForm = document.querySelector('.filter-card form');
        if (filterForm) {
            filterForm.addEventListener('submit', function () {
                if (window.location.hash) {
                    filterForm.action = '/admin/analytics' + window.location.hash;
                }
            });
        }
        
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

@endsection

