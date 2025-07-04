<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\Web\WebLoginController;

Route::get('/web-login', [WebLoginController::class, 'showLoginForm'])->name('login');
Route::post('/web-login', [WebLoginController::class, 'login'])->name('web-login.submit');
Route::post('/logout', [WebLoginController::class, 'logout'])->name('logout');
Route::get('/dashboard', [WebLoginController::class, 'dashboard'])->middleware('auth')->name('dashboard');
