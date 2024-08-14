<?php

use App\Http\Controllers\Api\V1\EVChargingStationsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::controller(EVChargingStationsController::class)->prefix('evcs')->group(function () {
        Route::get('nearby', 'nearby');
    });
});

// For future v2 integration:
// Route::middleware('auth:sanctum')->prefix('api/v2')->group(base_path('routes/api_v2.php'));
