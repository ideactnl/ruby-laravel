<?php

use App\Http\Controllers\Api\Participant\ParticipantWebApiController;
use App\Http\Controllers\PbacExportController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\RedirectIfParticipantUnauthenticated;
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

    // Route::get('/dashboard', function () {
    //     return view('participant.dashboard');
    // })->name('participant.dashboard');

    // Route::get('/pbac', function () {
    //     return view('participant.my-data');
    // })->name('participant.pbac');
    Route::middleware('auth.participant')->group(function () {
        Route::get('/dashboard', fn() => view('participant.dashboard'))->name('participant.dashboard');
        Route::get('/pbac', fn() => view('participant.my-data'))->name('participant.pbac');
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
});

/*
|--------------------------------------------------------------------------
| Superadmin Routes (Spatie Role Middleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::get('logs', [PbacExportController::class, 'index'])->name('logs');
});


/*
|--------------------------------------------------------------------------
| Researcher Routes (Spatie Role Middleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:researcher'])->group(function () {
    Route::get('pbac/export', [PbacExportController::class, 'showExportForm'])->name('pbac.export.form');
    Route::get('pbac/export/download', [PbacExportController::class, 'export'])->name('pbac.export');
});