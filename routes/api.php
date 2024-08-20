<?php

use App\Http\Controllers\Api\V1\EVChargingStationsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {

    // Authentication routes
    Route::prefix('auth')->withoutMiddleware('auth:sanctum')->group(function () {
        // Retrieve the limiter config for login attempts
        $limiter = config('fortify.limiters.login');

        Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('guest:' . config('fortify.guard'));
        Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware(
            array_filter(['guest:' . config('fortify.guard'), $limiter ? 'throttle:' . $limiter : null])
        );
    });

    Route::controller(EVChargingStationsController::class)->prefix('evcs')->group(function () {
        Route::get('nearby', 'nearby');
        Route::get('get-directions', 'getDirections');
    });
});

// For future v2 integration:
// Route::middleware('auth:sanctum')->prefix('api/v2')->group(base_path('routes/api_v2.php'));
