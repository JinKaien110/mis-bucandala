<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ArchiveController;

// Archive API (root): /api/v1/archive/*
Route::prefix('v1')->group(function () {
    Route::prefix('archive')->group(function () {

        // Keep method names consistent with ArchiveController
        Route::get('/residents', [ArchiveController::class, 'residents']);
        Route::get('/document-types', [ArchiveController::class, 'documentTypes']);
        Route::get('/document-requests', [ArchiveController::class, 'documentRequests']);
    });
});


