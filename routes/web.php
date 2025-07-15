<?php

use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return view('index');
});

use App\Http\Controllers\Api\Participant\ParticipantWebController;

Route::middleware(['web'])->group(function () {
    Route::get('/login', fn() => redirect()->route('participant.web.login'))->name('login');

    Route::prefix('participant')->name('participant.')->group(function () {
        Route::get('/web-login', [ParticipantWebController::class, 'showLoginForm'])->name('web.login');
        Route::post('/web-login', [ParticipantWebController::class, 'login'])->name('web.login.post');
        Route::post('/web-logout', [ParticipantWebController::class, 'logout'])->name('web.logout');

        Route::middleware(['auth:participant-web'])->group(function () {
            Route::get('/dashboard', [ParticipantWebController::class, 'dashboard'])->name('dashboard');
        });
    });
});