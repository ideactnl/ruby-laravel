<?php

use App\Http\Controllers\Api\MedicalSpecialist\MedicalSpecialistController;
use App\Http\Controllers\PbacExportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return view('index');
});


/*
|--------------------------------------------------------------------------
| Sanctum Web Auth Routes (For Participant)
|--------------------------------------------------------------------------
*/
Route::middleware(['web'])->prefix('participant')->group(function () {
    Route::get('/web-login', function () {
        if (Auth::guard('participant-web')->check()) {
            return redirect('/participant/dashboard');
        }
        return view('participant.web_login');
    })->name('participant.web.login');

    Route::middleware('auth.participant')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Api\Participant\ParticipantWebApiController::class, 'dashboardPage'])->name('participant.dashboard');
        Route::get('/export', [\App\Http\Controllers\Api\Participant\ParticipantWebApiController::class, 'exportPage'])->name('participant.export');
        Route::get('/daily-view', [\App\Http\Controllers\Api\Participant\ParticipantWebApiController::class, 'dailyViewPage'])->name('participant.daily-view');
        Route::get('/education', [\App\Http\Controllers\Api\Participant\ParticipantWebApiController::class, 'education'])->name('participant.education');
        Route::get('/self-management', [\App\Http\Controllers\Api\Participant\ParticipantWebApiController::class, 'selfManagement'])->name('participant.self-management');
        Route::get('/external-links', [\App\Http\Controllers\Api\Participant\ParticipantWebApiController::class, 'externalLinks'])->name('participant.external-links');
        Route::get('/general-information', [\App\Http\Controllers\Api\Participant\ParticipantWebApiController::class, 'generalInformation'])->name('participant.general-information');
        Route::get('/api/v1/participant/profile', [\App\Http\Controllers\Api\Participant\ParticipantWebApiController::class, 'profile'])->name('participant.api.profile');
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
Route::get('/admin', fn() => redirect()->to('/login'));
Auth::routes(['register' => false]);
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
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