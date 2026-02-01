<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

use App\Http\Controllers\Admin\ResidentController;
use App\Http\Controllers\Admin\DocumentTypeController;
use App\Http\Controllers\Admin\DocumentRequestController;
use App\Http\Controllers\Admin\DocumentRequestPrintController;
use App\Http\Controllers\Api\BlotterController;
/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::get('/auth/login', function () {
    return view('auth.login');
});

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

Route::middleware(['auth','admin'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | USERS (ADMINS / STAFF)
    |--------------------------------------------------------------------------
    */

    Route::view('/admin/users', 'admin.user-management.user');

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
});



    /*
    |--------------------------------------------------------------------------
    | DOCUMENT TYPES
    |--------------------------------------------------------------------------
    */

    Route::view('/admin/documents', 'admin.document-management.index')
        ->name('admin.document-types.index');

    Route::get('/admin/document-types', [DocumentTypeController::class, 'index'])
        ->name('admin.document-types.index');

    Route::post('/admin/document-types', [DocumentTypeController::class, 'store'])
        ->name('admin.document-types.store');

    Route::put('/admin/document-types/{documentType}', [DocumentTypeController::class, 'update'])
        ->name('admin.document-types.update');

    Route::patch('/admin/document-types/{documentType}/toggle', [DocumentTypeController::class, 'toggle'])
        ->name('admin.document-types.toggle');

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

    Route::get('/admin/document-requests/{documentRequest}/pdf',
        [DocumentRequestPrintController::class, 'pdf']
    )->name('admin.document-requests.pdf');

    Route::prefix('api/v1')->group(function () {

        Route::get('/document-requests', [DocumentRequestController::class, 'apiIndex']);
        Route::get('/document-requests/{documentRequest}', [DocumentRequestController::class, 'apiShow']);

        Route::get('/residents/options', [DocumentRequestController::class, 'residentOptions']);
        Route::get('/document-types/options', [DocumentRequestController::class, 'documentTypeOptions']);
    });


     /*
    |--------------------------------------------------------------------------
    | DOWNLOAD PDF
    |--------------------------------------------------------------------------
    */

    Route::middleware(['auth','admin'])->group(function () {
        Route::get('/admin/document-requests/{documentRequest}/download',
            [DocumentRequestPrintController::class, 'download']
        )->name('admin.document-requests.download');
    });


    /*
    |--------------------------------------------------------------------------
    | BLOTTERS (ADMIN)
    |--------------------------------------------------------------------------
    */

    Route::view('/admin/blotters', 'admin.blotter.index')->name('admin.blotter.index');

    Route::prefix('api/v1')->group(function () {
        Route::get('/blotters', [BlotterController::class, 'index']);
        Route::post('/blotters', [BlotterController::class, 'store']);
        Route::get('/blotters/{blotter}', [BlotterController::class, 'show']);
        Route::put('/blotters/{blotter}', [BlotterController::class, 'update']);
        Route::patch('/blotters/{blotter}/status', [BlotterController::class, 'updateStatus']);
    });

});

   