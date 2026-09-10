<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Storage files - serve directly from storage/app/public
Route::get('/storage/{file}', function ($file) {
    $fullPath = base_path('storage/app/public/'.$file);

    if (! file_exists($fullPath)) {
        abort(404);
    }

    $mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $fullPath);

    return response(file_get_contents($fullPath), 200, ['Content-Type' => $mime]);
})->where('file', '.+');

// Landing page - /public
Route::get('/', [App\Http\Controllers\Public\PublicController::class, 'home'])
    ->name('public.home')->middleware('blockAdmin');

// About page
Route::view('/about', 'public.about')
    ->name('public.about')->middleware('blockAdmin');

// Services page
Route::view('/services', 'public.services.index')
    ->name('public.services')->middleware('blockAdmin');

// Services - Documents
Route::view('/services/documents', 'public.services.documents')
    ->name('public.services.documents')->middleware('blockAdmin');

// Services - Blotter
Route::view('/services/blotter', 'public.services.blotter')
    ->name('public.services.blotter')->middleware('blockAdmin');

// Officials page
Route::view('/officials', 'public.officials')
    ->name('public.officials')->middleware('blockAdmin');

// News & Events page
Route::view('/news', 'public.news')
    ->name('public.news')->middleware('blockAdmin');

// FAQs page
Route::view('/faqs', 'public.faqs')
    ->name('public.faqs')->middleware('blockAdmin');

// Contact page
Route::view('/contact', 'public.contact')
    ->name('public.contact')->middleware('blockAdmin');

// Resident Registration
Route::view('/residents/register', 'public.residents.register')
    ->name('public.residents.register')->middleware('blockAdmin');

// Data Privacy
Route::view('/data-privacy', 'public.residents.data-privacy')
    ->name('public.data-privacy')->middleware('blockAdmin');

use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\BlotterController;
use App\Http\Controllers\Admin\CaseController;
use App\Http\Controllers\Admin\DocumentRequestController;
use App\Http\Controllers\Admin\DocumentRequestPrintController;
use App\Http\Controllers\Admin\DocumentTypeController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\HouseholdController;
use App\Http\Controllers\Admin\OfficialController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PetController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ResidentController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Public\OtpController;
use App\Http\Controllers\Public\ResidentRegistrationController;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::get('/auth/login', function () {
    $user = Auth::user();

    if ($user) {
        if($user->resident) {
            return redirect()->route('resident.dashboard') ?: redirect('/resident/dashboard');
        }
        if (in_array($user->admin->position, ['Barangay Captain', 'Barangay Secretary', 'Barangay Clerk'])) {
            // Always send admins to analytics; avoid crashing if a named route isn't registered
            return redirect('/admin/analytics');
        }
        if ($user->admin->position === 'Barangay Treasurer') {
            // Always send admins to analytics; avoid crashing if a named route isn't registered
            return redirect('/admin/payments');
        }
        if (in_array($user->admin->position, ['Lupon Member'])) {
            // Always send admins to analytics; avoid crashing if a named route isn't registered
            return redirect('/admin/blotters');
        }
        if (in_array($user->admin->position, ['Barangay Clerk'])) {
            // Always send admins to analytics; avoid crashing if a named route isn't registered
            return redirect('/admin/document-requests');
        }


        return redirect()->route('resident.dashboard') ?: redirect('/resident/dashboard');
    }

    return view('auth.login');
})->name('login');

Route::middleware(['auth', 'role:resident'])->group(function () {
    // Dashboard & Profile
    Route::get('/resident/dashboard', [App\Http\Controllers\Resident\DashboardController::class, 'index'])->name('resident.dashboard');
    Route::post('/resident/profile/update', [App\Http\Controllers\Resident\ProfileController::class, 'update'])->name('resident.profile.update');
    Route::get('/resident/profile', [App\Http\Controllers\Resident\ProfileController::class, 'edit'])->name('resident.profile');

    // Pets
    Route::get('/resident/pets', [App\Http\Controllers\Resident\PetController::class, 'index'])->name('resident.pets');
    Route::post('/resident/pets', [App\Http\Controllers\Resident\PetController::class, 'store'])->name('resident.pets.store');
    Route::put('/resident/pets/{pet}', [App\Http\Controllers\Resident\PetController::class, 'update'])->name('resident.pets.update');
    Route::delete('/resident/pets/{pet}', [App\Http\Controllers\Resident\PetController::class, 'destroy'])->name('resident.pets.destroy');

    // Household
    Route::get('/resident/household', [App\Http\Controllers\Resident\HouseholdController::class, 'index'])->name('resident.household');
    Route::post('/resident/household', [App\Http\Controllers\Resident\HouseholdController::class, 'store'])->name('resident.household.store');
    Route::put('/resident/household/{household}', [App\Http\Controllers\Resident\HouseholdController::class, 'update'])->name('resident.household.update');

    // Join Household by Code (for residents without household)
    Route::post('/resident/household/join', [App\Http\Controllers\Resident\HouseholdController::class, 'requestJoin'])->name('resident.household.join');

     // Add Member by Account No (for household head)
     Route::post('/resident/household/add-member', [App\Http\Controllers\Resident\HouseholdController::class, 'addMemberByAccount'])->name('resident.household.add-member');

     // Toggle PWD status for member
     Route::post('/resident/household/member/{member}/toggle-pwd', [App\Http\Controllers\Resident\HouseholdController::class, 'togglePWD'])->name('resident.household.member.toggle-pwd');

     // Bulk update household members details
     Route::put('/resident/household/members/bulk-update', [App\Http\Controllers\Resident\HouseholdController::class, 'bulkUpdateMembers'])->name('resident.household.members.bulk-update');

     // Update household member details
     Route::put('/resident/household/member/{member}', [App\Http\Controllers\Resident\HouseholdController::class, 'updateMember'])->name('resident.household.member.update');

     // Search residents by account number
     Route::get('/resident/household/search-resident', [App\Http\Controllers\Resident\HouseholdController::class, 'searchByAccount'])->name('resident.household.search-resident');

     // Approve/Reject Join Requests
    Route::post('/resident/household/request/{joinRequest}/approve', [App\Http\Controllers\Resident\HouseholdController::class, 'approveRequest'])->name('resident.household.request.approve');
    Route::post('/resident/household/request/{joinRequest}/reject', [App\Http\Controllers\Resident\HouseholdController::class, 'rejectRequest'])->name('resident.household.request.reject');
});

Route::get('/logout', function () {
    Auth::logout();

    return redirect()->route('login');
})->name('logout');

Route::post('/api/v1/auth/login', [AuthController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::get('/api/v1/auth/me', [AuthController::class, 'me']);
    Route::post('/api/v1/auth/logout', [AuthController::class, 'logout']);
});

/*
|--------------------------------------------------------------------------
| ADMIN DASHBOARD
|--------------------------------------------------------------------------
*/

// Backward-compatible API alias (no /admin prefix)
// Used by some frontend components to fetch data from the root /api/v1 path
Route::middleware(['auth', 'role:admin,staff'])->prefix('api/v1')->name('legacy.api.')->group(function () {

    Route::middleware([
        'ability:view-documents'
    ])->group(function () {
        Route::get('/residents/options', [DocumentRequestController::class, 'residentOptions']);
        // Document type options
        Route::get('/document-types/options', [DocumentRequestController::class, 'documentTypeOptions']);
        // ✅ Backward-compatible Document Requests API (fixes 404 on /api/v1/document-requests)
        Route::get('/document-requests', [DocumentRequestController::class, 'apiIndex']);
        Route::get('/document-requests/{documentRequest}', [DocumentRequestController::class, 'apiShow']);
        // ✅ Compatibility: Document Types API (fixes 404 on /api/v1/document-types)
        Route::get('/document-types', [DocumentTypeController::class, 'index']);
        Route::get('/document-types/{documentType}', [DocumentTypeController::class, 'show'])->whereNumber('documentType');

    });


    Route::middleware([
        'ability:view-core-records,manage-records,create-users,view-users'
    ])->group(function () {
        


        // Residents
        Route::get('/residents', [ResidentController::class, 'index']);
        Route::get('/residents/{resident}', [ResidentController::class, 'show']);
        Route::patch('/residents/{resident}/toggle-status', [ResidentController::class, 'toggleStatus']);
        Route::delete('/residents/{resident}', [ResidentController::class, 'destroy']);
        Route::post('/residents/{resident}/archive', [ResidentController::class, 'archive']);
        Route::post('/residents/{resident}/restore', [ResidentController::class, 'restore']);
        Route::get('/residents/options', [DocumentRequestController::class, 'residentOptions']);

        // Residents
        Route::get('/residents', [ResidentController::class, 'index']);
        Route::get('/residents/{resident}', [ResidentController::class, 'show']);
        Route::patch('/residents/{resident}/toggle-status', [ResidentController::class, 'toggleStatus']);
        Route::delete('/residents/{resident}', [ResidentController::class, 'destroy']);
        Route::post('/residents/{resident}/archive', [ResidentController::class, 'archive']);
        Route::post('/residents/{resident}/restore', [ResidentController::class, 'restore']);

    });


        
   

});



Route::middleware(['auth', 'role:admin,staff'])->prefix('admin')->name('admin.')->group(function () {


Route::middleware([

    'ability:view-dashboard'
])->group(function () {
        /*

        |--------------------------------------------------------------------------
        | ADMIN DASHBOARD (LANDING PAGE)
        |--------------------------------------------------------------------------
        */
    

        Route::get('/admin/dashboard', function () {
            return redirect()->route('admin.analytics');
        })->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | ANALYTICS DASHBOARD
        |--------------------------------------------------------------------------
        */
        Route::get('/analytics', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics');
        Route::get('/analytics/export', [App\Http\Controllers\Admin\AnalyticsController::class, 'export'])->name('analytics.export');

});


Route::middleware([
        'ability:view-households,view-community'
    ])->group(function () {
        /*
        |--------------------------------------------------------------------------
        | HOUSEHOLDS
        |--------------------------------------------------------------------------
        */
        Route::get('/households', [HouseholdController::class, 'index'])->name('households.index');
        Route::get('/households/create', [HouseholdController::class, 'create'])->name('households.create');
        Route::post('/households', [HouseholdController::class, 'store'])->name('households.store');
        Route::get('/households/{household}', [HouseholdController::class, 'show'])->name('households.show');
        Route::get('/households/{household}/edit', [HouseholdController::class, 'edit'])->name('households.edit');
        Route::put('/households/{household}', [HouseholdController::class, 'update'])->name('households.update');
        Route::delete('/households/{household}', [HouseholdController::class, 'destroy'])->name('households.destroy');
        Route::post('/households/{household}/restore', [HouseholdController::class, 'restore'])->name('households.restore');
        Route::post('/households/{household}/add-member', [HouseholdController::class, 'addMember'])->name('households.add-member');
        Route::delete('/households/{household}/members/{resident}', [HouseholdController::class, 'removeMember'])->name('households.remove-member');

        /*
        |--------------------------------------------------------------------------
        | ANNOUNCEMENTS
        |--------------------------------------------------------------------------
        */
        Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('/announcements/create', [AnnouncementController::class, 'create'])->name('announcements.create');
        Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::get('/announcements/{announcement}', [AnnouncementController::class, 'show'])->name('announcements.show');
        Route::get('/announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');
        Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
        Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
        Route::get('/announcements/modal/create', [AnnouncementController::class, 'getCreateData'])->name('announcements.modal.create');
        Route::get('/announcements/{announcement}/modal/edit', [AnnouncementController::class, 'getEditData'])->name('announcements.modal.edit');
        Route::get('/announcements/{announcement}/modal/show', [AnnouncementController::class, 'getShowData'])->name('announcements.modal.show');
    });
    

    Route::middleware([
        'ability:manage-users,create-users,view-users'
    ])->group(function () {
        Route::prefix('api/v1')->group(function () {

            Route::get('/users', [UserController::class, 'index']);
            Route::post('/users', [UserController::class, 'store']);
            Route::get('/users/{user}', [UserController::class, 'show']);
            Route::put('/users/{user}', [UserController::class, 'update']);
            Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus']);
            Route::patch('/users/{user}/reset-password', [UserController::class, 'resetPassword']);
        });

    });

    /*
    |--------------------------------------------------------------------------
    | RESIDENTS
    |--------------------------------------------------------------------------
    */

    Route::middleware([
        'ability:view-core-records'
    ])->group(function () {

        Route::view('/residents', 'admin.residents.index')
            ->name('residents');

        Route::post('/residents', [ResidentController::class, 'store'])
            ->name('residents.store');

        Route::put('/residents/{resident}', [ResidentController::class, 'update'])
            ->name('residents.update');

        Route::prefix('api/v1')->group(function () {

            // then list + show
            Route::get('/residents', [ResidentController::class, 'index']);
            Route::get('/residents/{resident}', [ResidentController::class, 'show']);
            Route::patch('/residents/{resident}/toggle-status', [ResidentController::class, 'toggleStatus']);
            Route::delete('/residents/{resident}', [ResidentController::class, 'destroy']);
            Route::post('/residents/{resident}/archive', [ResidentController::class, 'archive']);
            Route::post('/residents/{resident}/restore', [ResidentController::class, 'restore']);
        });
    });

    /*
    |--------------------------------------------------------------------------
    | DOCUMENT TYPES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin,staff'])->group(function () {
        Route::middleware([
            'ability:view-documents'
        ])->group(function () {
        
        Route::view('/document-types', 'admin.document-management.index')
            ->name('document-types.index');

        Route::get('/document-types/list', [DocumentTypeController::class, 'index'])->name('document-types.list');

        Route::post('/document-types', [DocumentTypeController::class, 'store'])
            ->name('document-types.store');

        Route::put('/document-types/{documentType}', [DocumentTypeController::class, 'update'])
                    ->name('admin.document-types.update');

        Route::patch('/document-types/{documentType}/toggleStatus', [DocumentTypeController::class, 'toggleStatus'])
                    ->name('admin.document-types.toggleStatus');

        Route::delete('/document-types/{documentType}', [DocumentTypeController::class, 'destroy'])
                    ->name('admin.document-types.destroy');

        // Existing API (mounted under /admin/api/v1)
        Route::prefix('api/v1')->group(function () {
            Route::get('/document-types', [DocumentTypeController::class, 'index']);

            Route::get('/document-types/{documentType}', [DocumentTypeController::class, 'show'])->whereNumber('documentType');

        });

        // ✅ Compatibility API (mounted under /admin/document-types/api/v1)
        // Fixes 404 when frontend calls: /admin/document-types/api/v1/document-types
        Route::prefix('document-types')->group(function () {
            Route::prefix('api/v1')->group(function () {
                Route::get('/document-types', [DocumentTypeController::class, 'index']);
                Route::get('/document-types/{documentType}', [DocumentTypeController::class, 'show'])->whereNumber('documentType');
            });
        });


        /*
        |--------------------------------------------------------------------------
        | DOCUMENT REQUESTS
        |--------------------------------------------------------------------------
        */

        Route::get('/document-requests', [DocumentRequestController::class, 'index'])
            ->name('document-requests.index');

        Route::post('/document-requests', [DocumentRequestController::class, 'store'])
            ->name('document-requests.store');

        Route::put('/document-requests/{documentRequest}', [DocumentRequestController::class, 'update'])
            ->name('document-requests.update');

        Route::patch('/document-requests/{documentRequest}/toggle', [DocumentRequestController::class, 'toggle'])
            ->name('document-requests.toggle');

        Route::delete('/document-requests/{documentRequest}', [DocumentRequestController::class, 'destroy'])
            ->name('document-requests.destroy');

         Route::get('/admin/document-requests/{documentRequest}/patawag',
             [DocumentRequestPrintController::class, 'patawagComplainant']
         )->name('admin.document-requests.patawag');
        });



        Route::prefix('api/v1')->group(function () {

            Route::middleware([
                'ability:view-documents'
            ])->group(function () {
            // NOTE: archive routes here are mounted under: /admin/api/v1/archive/*
            Route::get('/document-requests', [DocumentRequestController::class, 'apiIndex']);
            Route::get('/document-requests/{documentRequest}', [DocumentRequestController::class, 'apiShow']);

             // Archive API (mounted under /admin/api/v1)
             Route::get('/archive/residents', [App\Http\Controllers\Admin\ArchiveController::class, 'residents']);
             Route::get('/archive/document-types', [App\Http\Controllers\Admin\ArchiveController::class, 'documentTypes']);
             Route::get('/archive/document-requests', [App\Http\Controllers\Admin\ArchiveController::class, 'documentRequests']);


            });
            


             // Restore endpoints
             Route::post('/residents/{resident}/restore', [App\Http\Controllers\Admin\ResidentController::class, 'restore']);
             Route::post('/document-types/{documentType}/restore', [App\Http\Controllers\Admin\DocumentTypeController::class, 'restore']);
             Route::post('/document-requests/{documentRequest}/restore', [App\Http\Controllers\Admin\DocumentRequestController::class, 'restore']);
         });
    });

    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD PDF
    |--------------------------------------------------------------------------
    */

     Route::middleware(['auth', 'role:admin,staff'])->group(function () {
         Route::get('/document-requests/{documentRequest}/download',
             [DocumentRequestPrintController::class, 'download']
         )->name('document-requests.download');

         Route::get('/document-requests/{documentRequest}/print',
             [DocumentRequestPrintController::class, 'print']
         )->name('document-requests.print');
     });

    
    
    Route::middleware([
                'ability:view-blotters'
            ])->group(function () {
     /*
    
    |--------------------------------------------------------------------------
    | BLOTTERS (ADMIN)
    |--------------------------------------------------------------------------
    */

        // Blotters
        Route::get('/blotters', [BlotterController::class, 'index'])->name('blotters.index');
        Route::get('/blotters/create', [BlotterController::class, 'create'])->name('blotters.create');
        Route::post('/blotters', [BlotterController::class, 'store'])->name('blotters.store');
        Route::get('/blotters/{blotter}', [BlotterController::class, 'show'])->name('blotters.show');
        Route::put('/blotters/{blotter}', [BlotterController::class, 'update'])->name('blotters.update');

        // Convert blotter -> case (ongoing)
        Route::post('/blotters/{blotter}/open-case', [BlotterController::class, 'openCase'])->name('blotters.openCase');

        // Cases
        Route::get('/cases', [CaseController::class, 'index'])->name('cases.index');
        Route::get('/cases/{case}', [CaseController::class, 'show'])->name('cases.show');

        // Hearings
        Route::post('/cases/{case}/hearings', [CaseController::class, 'storeHearing'])->name('cases.hearings.store');

        // Close
        Route::post('/cases/{case}/close', [CaseController::class, 'close'])->name('cases.close');
        });




        Route::middleware([
                'ability:view-dashboard'
            ])->group(function () {
        /*
        |--------------------------------------------------------------------------
        | ACTIVITY LOGS
        |--------------------------------------------------------------------------
        */
        Route::get('/logs', [AuditLogController::class, 'index'])->name('logs.index');

        // Officials - API/Modal routes
        Route::get('/officials', [OfficialController::class, 'index'])->name('officials.index');
        Route::post('/officials', [OfficialController::class, 'store'])->name('officials.store');
        Route::put('/officials/{official}', [OfficialController::class, 'update'])->name('officials.update');
        Route::delete('/officials/{official}', [OfficialController::class, 'destroy'])->name('officials.destroy');

        // Officials Modal APIs
        Route::get('/officials/modal/create', [OfficialController::class, 'getCreateData'])->name('officials.modal.create');
        Route::get('/officials/{official}/modal/edit', [OfficialController::class, 'getEditData'])->name('officials.modal.edit');
        Route::get('/officials/{official}/modal/show', [OfficialController::class, 'getShowData'])->name('officials.modal.show');
        Route::get('/officials/api/active', [OfficialController::class, 'activeOfficials']);


        // Official Terms - API/Modal routes
        Route::get('/officials/terms', [OfficialController::class, 'termsIndex'])->name('officials.terms.index');
        Route::post('/officials/terms', [OfficialController::class, 'storeTerm'])->name('officials.terms.store');
        Route::put('/officials/terms/{term}', [OfficialController::class, 'updateTerm'])->name('officials.terms.update');
        Route::post('/officials/terms/{term}/archive', [OfficialController::class, 'archiveTerm'])->name('officials.terms.archive');
        Route::post('/officials/terms/{term}/unarchive', [OfficialController::class, 'unarchiveTerm'])->name('officials.terms.unarchive');

        // Official Terms Modal APIs
        Route::get('/officials/terms/modal/create', [OfficialController::class, 'getTermCreateData'])->name('officials.terms.modal.create');
        Route::get('/officials/terms/{term}/modal/edit', [OfficialController::class, 'getTermEditData'])->name('officials.terms.modal.edit');

        // Events
        Route::get('/events', [EventController::class, 'index'])->name('events.index');
        Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
        Route::post('/events', [EventController::class, 'store'])->name('events.store');
        Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
        Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
        Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

        // Pets
        Route::get('/pets', [PetController::class, 'index'])->name('pets.index');
        Route::get('/pets/create', [PetController::class, 'create'])->name('pets.create');
        Route::post('/pets', [PetController::class, 'store'])->name('pets.store');
        Route::get('/pets/{pet}', [PetController::class, 'show'])->name('pets.show');
        Route::get('/pets/{pet}/edit', [PetController::class, 'edit'])->name('pets.edit');
        Route::put('/pets/{pet}', [PetController::class, 'update'])->name('pets.update');
        Route::delete('/pets/{pet}', [PetController::class, 'destroy'])->name('pets.destroy');

        });


        
            Route::middleware([
                'ability:view-payments'
            ])->group(function () {
                // Payments / Fees
                Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
                // NOTE: Removed create/store endpoints to hide/disable "Add Payment" button
                // Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
                // Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');

                Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
                Route::get('/payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
                Route::put('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
                Route::post('/payments/{payment}/mark-paid', [PaymentController::class, 'markAsPaid'])->name('payments.markPaid');
                Route::post('/payments/{payment}/cancel', [PaymentController::class, 'cancel'])->name('payments.cancel');
                Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
            });


            Route::middleware([
                'ability:view-core-records'
            ])->group(function () {
        // Users & Roles

            Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
            Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');
            Route::post('/users/{user}/reset-password', [UserController::class, 'updatePassword'])->name('users.resetPassword');
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

         // Reports (Admin/Captain & Staff/Secretary)
             Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
             Route::get('/reports/residents', [ReportController::class, 'residents'])->name('reports.residents');
             Route::get('/reports/financial', [ReportController::class, 'financial'])->name('reports.financial');
             Route::get('/reports/blotters', [ReportController::class, 'blotters'])->name('reports.blotters');
             Route::get('/reports/documents', [ReportController::class, 'documents'])->name('reports.documents');
          // Archive
          Route::get('/archive', [App\Http\Controllers\Admin\ArchiveController::class, 'index'])->name('archive.index');
        });    
      });
use App\Http\Controllers\Admin\CasePrintController;

Route::get('/admin/cases/{case}/cert-to-file-action/docx', [CasePrintController::class, 'certToFileAction'])
    ->name('admin.cases.cert_to_file_action.docx');

Route::prefix('api/v1/public')->group(function () {
    Route::post('/otp/send', [OtpController::class, 'send']);
    Route::post('/otp/verify', [OtpController::class, 'verify']);
    Route::post('/residents/register', [ResidentRegistrationController::class, 'register']);
})->middleware('blockAdmin');


