<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Pbac\PbacController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Participant\ParticipantWebApiController;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

Route::prefix('v1')->group(function () {
    Route::middleware('throttle:5,1')->group(function () {
        // Route::post('/register', [AuthController::class, 'register']);
        Route::post('/register', [AuthController::class, 'registerNew']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::patch('/profile', [AuthController::class, 'updateProfile']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::delete('/account', [AuthController::class, 'deleteAccount']);
        Route::post('/medical-specialist/access', [AuthController::class, 'enableMedicalSpecialistAccess']);
        
        Route::middleware('throttle:60,1')->group(function () {
            Route::get('/pbac/filter', [PbacController::class, 'filter']);
            Route::get('/pbac/check', [PbacController::class, 'check']);
            Route::apiResource('pbac', PbacController::class)->except(['destroy', 'update']);
        });
    });

    Route::prefix('participant')->middleware([EnsureFrontendRequestsAreStateful::class, ])->group(function () {
        Route::post('/refresh-session', [ParticipantWebApiController::class, 'refreshSession'])->name('participant.refresh.session');
        Route::post('/login', [ParticipantWebApiController::class, 'login']);
        Route::post('/logout', [ParticipantWebApiController::class, 'logout']);
        Route::post('/dashboard-login', [ParticipantWebApiController::class, 'dashboardLogin'])->name('participant.dashboard.login');
        Route::get('/videos/education', [ParticipantWebApiController::class, 'getEducationVideos'])->name('participant.videos.education');
        Route::get('/videos/self-management', [ParticipantWebApiController::class, 'getSelfManagementVideos'])->name('participant.videos.self-management');
        Route::post('/videos/fetch', [ParticipantWebApiController::class, 'fetchVideos'])->name('participant.videos.fetch.api');
        Route::get('/cms/categories/filter', [ParticipantWebApiController::class, 'fetchCategories'])->name('participant.categories.filter.api');

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/dashboard', [ParticipantWebApiController::class, 'dashboard']);
            Route::get('/menstruation-wrapped', [ParticipantWebApiController::class, 'getMenstruationWrapped'])->name('participant.menstruation-wrapped');
            Route::get('/pbac/export', [ParticipantWebApiController::class, 'exportPbacData'])->name('participant.pbac.export');
            Route::post('/pbac/chart/export/pdf', [ParticipantWebApiController::class, 'exportChartPdf'])->name('participant.pbac.chart.export.pdf');
            Route::get('/daily', [ParticipantWebApiController::class, 'dailyData'])->name('participant.daily');
            Route::get('/videos/daily-view', [ParticipantWebApiController::class, 'getDailyViewVideos'])->name('participant.videos.daily-view');
            Route::get('/exports/active', [ParticipantWebApiController::class, 'activeExport'])->name('participant.exports.active');
            Route::get('/exports/{jobId}', [ParticipantWebApiController::class, 'exportStatus'])->name('participant.exports.status');
            Route::get('/exports/{jobId}/download', [ParticipantWebApiController::class, 'downloadExport'])->middleware('signed')->name('participant.exports.download');
        });
    });
});

