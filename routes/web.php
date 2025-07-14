<?php

use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return view('index');
});

use App\Http\Controllers\Api\Participant\ParticipantWebController;

Route::get('/participant/web-login', [ParticipantWebController::class, 'showLoginForm'])->name('participant.web-login');

Route::get('/login', function () {
    return redirect()->route('participant.web-login');
})->name('login');


Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/participant/dashboard', [ParticipantWebController::class, 'dashboard'])->name('participant.dashboard');
});