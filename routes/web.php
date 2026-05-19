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
        if (in_array($user->role, ['admin', 'staff'])) {
            return redirect()->route('admin.dashboard') ?: redirect('/admin/residents');
        }

        return redirect()->route('resident.dashboard') ?: redirect('/resident/dashboard');
    }

    return view('auth.login');
})->name('login');

Route::middleware(['auth', 'role:resident'])->group(function () {
    // Dashboard & Profile
    Route::get('/resident/dashboard', [App\Http\Controllers\Resident\DashboardController::class, 'index'])->name('resident.dashboard');
    Route::post('/resident/profile/update', [App\Http\Controllers\Resident\DashboardController::class, 'updateProfile'])->name('resident.profile.update');
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

Route::middleware(['auth', 'role:admin,staff'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | ADMIN DASHBOARD (LANDING PAGE)
    |--------------------------------------------------------------------------
    */
    Route::get('/admin', function () {
        return redirect()->route('admin.analytics');
    })->name('admin.dashboard');

    Route::get('/admin/dashboard', function () {
        return redirect()->route('admin.analytics');
    });

    /*
    |--------------------------------------------------------------------------
    | ANALYTICS DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::get('/admin/analytics', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('admin.analytics');
    Route::get('/admin/analytics/export', [App\Http\Controllers\Admin\AnalyticsController::class, 'export'])->name('admin.analytics.export');

    /*
    |--------------------------------------------------------------------------
    | HOUSEHOLDS
    |--------------------------------------------------------------------------
    */
    Route::get('/admin/households', [HouseholdController::class, 'index'])->name('admin.households.index');
    Route::get('/admin/households/create', [HouseholdController::class, 'create'])->name('admin.households.create');
    Route::post('/admin/households', [HouseholdController::class, 'store'])->name('admin.households.store');
    Route::get('/admin/households/{household}', [HouseholdController::class, 'show'])->name('admin.households.show');
    Route::get('/admin/households/{household}/edit', [HouseholdController::class, 'edit'])->name('admin.households.edit');
    Route::put('/admin/households/{household}', [HouseholdController::class, 'update'])->name('admin.households.update');
    Route::delete('/admin/households/{household}', [HouseholdController::class, 'destroy'])->name('admin.households.destroy');
    Route::post('/admin/households/{household}/restore', [HouseholdController::class, 'restore'])->name('admin.households.restore');
    Route::post('/admin/households/{household}/add-member', [HouseholdController::class, 'addMember'])->name('admin.households.add-member');
    Route::delete('/admin/households/{household}/members/{resident}', [HouseholdController::class, 'removeMember'])->name('admin.households.remove-member');

    /*
    |--------------------------------------------------------------------------
    | ANNOUNCEMENTS
    |--------------------------------------------------------------------------
    */
    Route::get('/admin/announcements', [AnnouncementController::class, 'index'])->name('admin.announcements.index');
    Route::get('/admin/announcements/create', [AnnouncementController::class, 'create'])->name('admin.announcements.create');
    Route::post('/admin/announcements', [AnnouncementController::class, 'store'])->name('admin.announcements.store');
    Route::get('/admin/announcements/{announcement}', [AnnouncementController::class, 'show'])->name('admin.announcements.show');
    Route::get('/admin/announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('admin.announcements.edit');
    Route::put('/admin/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('admin.announcements.update');
    Route::delete('/admin/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('admin.announcements.destroy');
    Route::get('/admin/announcements/modal/create', [AnnouncementController::class, 'getCreateData'])->name('admin.announcements.modal.create');
    Route::get('/admin/announcements/{announcement}/modal/edit', [AnnouncementController::class, 'getEditData'])->name('admin.announcements.modal.edit');
    Route::get('/admin/announcements/{announcement}/modal/show', [AnnouncementController::class, 'getShowData'])->name('admin.announcements.modal.show');

    Route::prefix('api/v1')->group(function () {

        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus']);
        Route::patch('/users/{user}/reset-password', [UserController::class, 'resetPassword']);
    });

    /*
    |--------------------------------------------------------------------------
    | RESIDENTS
    |--------------------------------------------------------------------------
    */

    Route::view('/admin/residents', 'admin.residents.index')
        ->name('admin.residents');

    Route::post('/admin/residents', [ResidentController::class, 'store'])
        ->name('admin.residents.store');

    Route::put('/admin/residents/{resident}', [ResidentController::class, 'update'])
        ->name('admin.residents.update');

    Route::prefix('api/v1')->group(function () {

        // ✅ put OPTIONS FIRST
        Route::get('/residents/options', [DocumentRequestController::class, 'residentOptions']);

        // then list + show
        Route::get('/residents', [ResidentController::class, 'index']);
        Route::get('/residents/{resident}', [ResidentController::class, 'show']);
         Route::patch('/residents/{resident}/toggle-status', [ResidentController::class, 'toggleStatus']);
         Route::delete('/residents/{resident}', [ResidentController::class, 'destroy']);
         Route::post('/residents/{resident}/archive', [ResidentController::class, 'archive']);
         Route::post('/residents/{resident}/restore', [ResidentController::class, 'restore']);
    });

    /*
    |--------------------------------------------------------------------------
    | DOCUMENT TYPES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin,staff'])->group(function () {
        Route::view('/admin/documents', 'admin.document-management.index')
            ->name('admin.document-types.index');

        Route::get('/admin/document-types', [DocumentTypeController::class, 'index'])
            ->name('admin.document-types.index');

        Route::post('/admin/document-types', [DocumentTypeController::class, 'store'])
            ->name('admin.document-types.store');

        Route::put('/admin/document-types/{documentType}', [DocumentTypeController::class, 'update'])
            ->name('admin.document-types.update');

        Route::patch('/admin/document-types/{documentType}/toggleStatus', [DocumentTypeController::class, 'toggleStatus'])
            ->name('admin.document-types.toggleStatus');

        Route::delete('/admin/document-types/{documentType}', [DocumentTypeController::class, 'destroy'])
            ->name('admin.document-types.destroy');

        Route::prefix('api/v1')->group(function () {
            Route::get('/document-types', [DocumentTypeController::class, 'index']);

            Route::get('/document-types/{documentType}', [DocumentTypeController::class, 'show'])->whereNumber('documentType');

        });

        /*
        |--------------------------------------------------------------------------
        | DOCUMENT REQUESTS
        |--------------------------------------------------------------------------
        */

        Route::get('/admin/document/requests', [DocumentRequestController::class, 'index'])
            ->name('admin.document-requests.index');

        Route::post('/admin/document/requests', [DocumentRequestController::class, 'store'])
            ->name('admin.document-requests.store');

        Route::put('/admin/document/requests/{documentRequest}', [DocumentRequestController::class, 'update'])
            ->name('admin.document-requests.update');

        Route::patch('/admin/document/requests/{documentRequest}/toggle', [DocumentRequestController::class, 'toggle'])
            ->name('admin.document-requests.toggle');

        Route::delete('/admin/document/requests/{documentRequest}', [DocumentRequestController::class, 'destroy'])
            ->name('admin.document-requests.destroy');

         Route::get('/admin/document-requests/{documentRequest}/patawag',
             [DocumentRequestPrintController::class, 'patawagComplainant']
         )->name('admin.document-requests.patawag');

        Route::prefix('api/v1')->group(function () {

            Route::get('/document-requests', [DocumentRequestController::class, 'apiIndex']);
            Route::get('/document-requests/{documentRequest}', [DocumentRequestController::class, 'apiShow']);

             Route::get('/residents/options', [DocumentRequestController::class, 'residentOptions']);
             Route::get('/document-types/options', [DocumentRequestController::class, 'documentTypeOptions']);

             // Archive API
             Route::get('/archive/residents', [App\Http\Controllers\Admin\ArchiveController::class, 'residents']);
             Route::get('/archive/document-types', [App\Http\Controllers\Admin\ArchiveController::class, 'documentTypes']);
             Route::get('/archive/document-requests', [App\Http\Controllers\Admin\ArchiveController::class, 'documentRequests']);

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
         Route::get('/admin/document-requests/{documentRequest}/download',
             [DocumentRequestPrintController::class, 'download']
         )->name('admin.document-requests.download');

         Route::get('/admin/document-requests/{documentRequest}/print',
             [DocumentRequestPrintController::class, 'print']
         )->name('admin.document-requests.print');
     });

    /*
    |--------------------------------------------------------------------------
    | BLOTTERS (ADMIN)
    |--------------------------------------------------------------------------
    */

    // routes/web.php

    Route::middleware(['auth', 'role:admin,staff'])->prefix('admin')->name('admin.')->group(function () {

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

        /*
        |--------------------------------------------------------------------------
        | ACTIVITY LOGS
        |--------------------------------------------------------------------------
        */
        Route::get('/admin/logs', [AuditLogController::class, 'index'])->name('logs.index');

        // Officials - API/Modal routes
        Route::get('/officials', [OfficialController::class, 'index'])->name('officials.index');
        Route::post('/officials', [OfficialController::class, 'store'])->name('officials.store');
        Route::put('/officials/{official}', [OfficialController::class, 'update'])->name('officials.update');
        Route::delete('/officials/{official}', [OfficialController::class, 'destroy'])->name('officials.destroy');

        // Officials Modal APIs
        Route::get('/officials/modal/create', [OfficialController::class, 'getCreateData'])->name('officials.modal.create');
        Route::get('/officials/{official}/modal/edit', [OfficialController::class, 'getEditData'])->name('officials.modal.edit');
        Route::get('/officials/{official}/modal/show', [OfficialController::class, 'getShowData'])->name('officials.modal.show');

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

        // Payments / Fees
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::get('/payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
        Route::put('/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
        Route::post('/payments/{payment}/mark-paid', [PaymentController::class, 'markAsPaid'])->name('payments.markPaid');
        Route::post('/payments/{payment}/cancel', [PaymentController::class, 'cancel'])->name('payments.cancel');
        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');

        // Users & Roles
        Route::middleware(['captain'])->group(function () {
            Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
        });

        Route::middleware(['hasRole:admin,staff'])->group(function () {
            Route::get('/users', [UserController::class, 'index'])->name('users.index');
            Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        });

        Route::middleware(['captain'])->group(function () {
            Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');
            Route::post('/users/{user}/reset-password', [UserController::class, 'updatePassword'])->name('users.resetPassword');
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        });

         // Reports (Admin/Captain & Staff/Secretary)
         Route::middleware(['hasRole:admin,staff'])->group(function () {
             Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
             Route::get('/reports/residents', [ReportController::class, 'residents'])->name('reports.residents');
             Route::get('/reports/financial', [ReportController::class, 'financial'])->name('reports.financial');
             Route::get('/reports/blotters', [ReportController::class, 'blotters'])->name('reports.blotters');
             Route::get('/reports/documents', [ReportController::class, 'documents'])->name('reports.documents');
         });

          // Archive
          Route::get('/archive', [App\Http\Controllers\Admin\ArchiveController::class, 'index'])->name('archive.index');
      });

});

Route::prefix('api/v1/public')->group(function () {
    Route::post('/otp/send', [OtpController::class, 'send']);
    Route::post('/otp/verify', [OtpController::class, 'verify']);
})->middleware('blockAdmin');

Route::prefix('api/v1/public')->group(function () {
    Route::post('/residents/register', [ResidentRegistrationController::class, 'register']);
})->middleware('blockAdmin');

use App\Http\Controllers\Admin\CasePrintController;

Route::get('/admin/cases/{case}/cert-to-file-action/docx', [CasePrintController::class, 'certToFileAction'])
    ->name('admin.cases.cert_to_file_action.docx');
