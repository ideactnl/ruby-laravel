<?php

use App\Http\Controllers\Admin\AdminerController;
use App\Http\Controllers\Admin\AdminerVerificationController;
use App\Http\Controllers\Api\MedicalSpecialist\MedicalSpecialistController;
use App\Http\Controllers\Api\Participant\ParticipantWebApiController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PbacExportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

/*
|--------------------------------------------------------------------------
| Language Switch
|--------------------------------------------------------------------------
*/
Route::post('/switch-language', [LanguageController::class, 'switch'])->name('switch-language');

/*
|--------------------------------------------------------------------------
| Participant Routes
|--------------------------------------------------------------------------
*/
Route::prefix('participant')->middleware(['web'])->group(function () {

    Route::get('/app-login', [ParticipantWebApiController::class, 'appLogin'])->name('participant.app.login');
    Route::get('/web-login', [ParticipantWebApiController::class, 'webLogin'])->name('participant.web.login');

    Route::middleware(['auth.participant', 'api.login.expiry'])->group(function () {

        Route::get('/dashboard', [ParticipantWebApiController::class, 'dashboardPage'])->name('participant.dashboard');
        Route::get('/export', [ParticipantWebApiController::class, 'exportPage'])->name('participant.export');
        Route::get('/daily-view', [ParticipantWebApiController::class, 'dailyViewPage'])->name('participant.daily-view');
        Route::get('/education', [ParticipantWebApiController::class, 'education'])->name('participant.education');
        Route::get('/self-management', [ParticipantWebApiController::class, 'selfManagement'])->name('participant.self-management');
        Route::get('/external-links', [ParticipantWebApiController::class, 'externalLinks'])->name('participant.external-links');
        Route::get('/general-information', [ParticipantWebApiController::class, 'generalInformation'])->name('participant.general-information');
        Route::get('/settings', [ParticipantWebApiController::class, 'settings'])->name('participant.settings');

        Route::get('/api/v1/participant/profile', [ParticipantWebApiController::class, 'profile'])->name('participant.api.profile');
    });
});

/*
|--------------------------------------------------------------------------
| Medical Specialist Routes
|--------------------------------------------------------------------------
*/
Route::prefix('medical-specialist')->name('medical-specialist.')->group(function () {

    Route::get('/login', [MedicalSpecialistController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [MedicalSpecialistController::class, 'login'])->name('login.submit');

    Route::middleware('auth.medical-specialist')->group(function () {
        Route::get('/dashboard', [MedicalSpecialistController::class, 'dashboard'])->name('dashboard');
        Route::get('/chart-data', [MedicalSpecialistController::class, 'getChartData'])->name('chart-data');
        Route::get('/export', [MedicalSpecialistController::class, 'exportPbacData'])->name('export');
        Route::post('/export-pdf', [MedicalSpecialistController::class, 'exportPbacPdf'])->name('export.pdf');
        Route::get('/exports/active', [MedicalSpecialistController::class, 'activeExport'])->name('exports.active');
        Route::get('/exports/{jobId}', [MedicalSpecialistController::class, 'exportStatus'])->name('exports.status');
        Route::get('/exports/{jobId}/download', [MedicalSpecialistController::class, 'downloadExport'])->middleware('signed')->name('exports.download');
        Route::post('/logout', [MedicalSpecialistController::class, 'logout'])->name('logout');
    });
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Laravel UI)
|--------------------------------------------------------------------------
*/
Route::get('/admin', fn () => redirect()->to('/login'));
Auth::routes(['register' => false]);

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    Route::post('/profile', [UserController::class, 'updateSelf'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Superadmin Routes (Spatie Role Middleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::get('logs', [PbacExportController::class, 'logs'])->name('logs');
});

/*
|--------------------------------------------------------------------------
| Database Management Security Layer (Adminer)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:adminer_user'])->group(function () {
    Route::get('db-verify', [AdminerVerificationController::class, 'show'])->name('admin.db-verify.show');
    Route::post('db-verify', [AdminerVerificationController::class, 'verifyChallenge'])->name('admin.db-verify.challenge');

    Route::middleware(['adminer.sudo', 'adminer.gate'])->group(function () {
        Route::any('database', [AdminerController::class, 'index'])->name('admin.database.index');
    });
});

/*
|--------------------------------------------------------------------------
| Researcher Routes (Spatie Role Middleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:researcher|superadmin'])->group(function () {
    Route::get('pbac/export', [PbacExportController::class, 'showExportForm'])->name('pbac.export.form');
    Route::post('pbac/export/queue', [PbacExportController::class, 'queue'])->name('admin.pbac.exports.queue');
    Route::get('pbac/exports/active', [PbacExportController::class, 'active'])->name('admin.pbac.exports.active');
    Route::get('pbac/exports/recent', [PbacExportController::class, 'recent'])->name('admin.pbac.exports.recent');
    Route::get('pbac/exports/{jobId}', [PbacExportController::class, 'status'])->name('admin.pbac.exports.status');
    Route::get('pbac/exports/{jobId}/download', [PbacExportController::class, 'download'])->middleware('signed')->name('admin.pbac.exports.download');
});
