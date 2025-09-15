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
        Route::get('/dashboard', fn() => view('participant.dashboard'))->name('participant.dashboard');
        Route::get('/pbac', fn() => view('participant.export-my-data'))->name('participant.pbac');
        Route::get('/daily-view', fn() => view('participant.daily-view'))->name('participant.daily-view');
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
    Route::get('/dashboard', [MedicalSpecialistController::class, 'dashboard'])->name('dashboard');
    Route::get('/export', [MedicalSpecialistController::class, 'exportPbacData'])->name('export');
    Route::post('/export-pdf', [MedicalSpecialistController::class, 'exportPbacPdf'])->name('export.pdf');
    Route::post('/logout', [MedicalSpecialistController::class, 'logout'])->name('logout');
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
Route::middleware(['auth', 'role:researcher'])->group(function () {
    Route::get('pbac/export', [PbacExportController::class, 'showExportForm'])->name('pbac.export.form');

    // Queue-based researcher exports
    Route::post('pbac/export/queue', [PbacExportController::class, 'queue'])->name('admin.pbac.exports.queue');
    Route::get('pbac/exports/active', [PbacExportController::class, 'active'])->name('admin.pbac.exports.active');
    Route::get('pbac/exports/{jobId}', [PbacExportController::class, 'status'])->name('admin.pbac.exports.status');
    Route::get('pbac/exports/{jobId}/download', [PbacExportController::class, 'download'])->middleware('signed')->name('admin.pbac.exports.download');
});