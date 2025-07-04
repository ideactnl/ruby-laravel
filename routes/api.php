<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Pbac\PbacController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::middleware('throttle:5,1')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::patch('/profile', [AuthController::class, 'updateProfile']);
        Route::post('/logout', [AuthController::class, 'logout']);

        /**
         * PBAC Routes
         */
        Route::get('pbac/filter', [PbacController::class, 'filter']);
        Route::get('pbac/check', [PbacController::class, 'check']);
        Route::apiResource('pbac', PbacController::class)->except(['destroy', 'update']);

    });

    Route::post('/login-logs', [AuthController::class, 'loginLogs']);

});
