<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Pbac\PbacController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Participant\ParticipantWebApiController;

Route::prefix('v1')->group(function () {
    Route::middleware('throttle:5,1')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });
    Route::post('/login-logs', [AuthController::class, 'loginLogs']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::patch('/profile', [AuthController::class, 'updateProfile']);
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::post('/medical-specialist/access', [AuthController::class, 'enableMedicalSpecialistAccess']);
        Route::get('/pbac/filter', [PbacController::class, 'filter']);
        Route::get('/pbac/check', [PbacController::class, 'check']);
        Route::apiResource('pbac', PbacController::class)->except(['destroy', 'update']);
    });

    Route::prefix('participant')->group(function () {
        Route::post('/login', [ParticipantWebApiController::class, 'login']);
        Route::post('/logout', [ParticipantWebApiController::class, 'logout']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/dashboard', [ParticipantWebApiController::class, 'dashboard']);
            Route::get('/pbac/export', [ParticipantWebApiController::class, 'exportPbacData'])->name('participant.pbac.export');
            Route::post('/pbac/chart/export/pdf', [ParticipantWebApiController::class, 'exportChartPdf'])->name('participant.pbac.chart.export.pdf');
            Route::get('/daily', [ParticipantWebApiController::class, 'dailyData'])->name('participant.daily');
        });
    });
});
