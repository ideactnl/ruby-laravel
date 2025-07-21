<?php

use App\Http\Controllers\Api\Participant\ParticipantWebApiController;
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
Route::middleware(['web'])->group(function () {
    Route::get('/participant/web-login', [ParticipantWebApiController::class, 'showLoginForm'])
        ->name('participant.web.login');

    Route::get('/participant/dashboard', function () {
        return view('participant.dashboard');
    })->name('participant.dashboard');
});


/*
|--------------------------------------------------------------------------
| Authenticated Routes (Laravel UI)
|--------------------------------------------------------------------------
*/
Route::get('/admin', fn() => redirect()->to('/login'));
Auth::routes(['register' => false]);
Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');


/*
|--------------------------------------------------------------------------
| Superadmin Routes (Spatie Role Middleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Superadmin'])->group(function () {
    Route::resource('users', UserController::class);
});