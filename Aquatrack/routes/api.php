<?php

use App\Http\Controllers\Admin\WaterRateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Reports notifications endpoint
Route::middleware('auth:sanctum')->group(function () {
    // Reports notifications endpoint
    Route::get('/reports/notifications', [NotificationController::class, 'getReports'])
        ->name('api.reports.notifications');
    // routes/api.php
    Route::get('/notifications', [NotificationController::class, 'getNotificationsApi']);


    // Water rates API for staff reading calculations
    Route::get('/water-rates/active', [WaterRateController::class, 'getActiveRates'])->name('api.water-rates.active');



    // Existing report API routes
    Route::get('/reports/find', [ReportController::class, 'findByTrackingCode'])
        ->name('api.reports.find');
});

// Public API routes (if any)
Route::middleware('api')->group(function () {
    // Add any public API routes here
});
