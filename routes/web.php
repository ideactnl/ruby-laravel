<?php

use App\Http\Controllers\Api\Participant\ParticipantWebApiController;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return view('index');
});

Route::middleware(['web'])->group(function () {
    Route::get('/login', fn() => redirect()->route('participant.web.login'))->name('login');

    Route::get('/participant/web-login', [ParticipantWebApiController::class, 'showLoginForm'])->name('participant.web.login');
    Route::get('/participant/dashboard', function () {
        return view('dashboard'); 
    })->name('participant.dashboard');
});