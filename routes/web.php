<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ReklameController;

Route::get('/', function () {
    return redirect()->route('reklames.index');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/daftar-reklame/export-excel', [ReklameController::class, 'exportExcel'])->name('reklames.exportExcel');
    Route::get('/daftar-reklame', [ReklameController::class, 'list'])->name('reklames.list');
    Route::resource('reklames', ReklameController::class)->except(['index', 'show']);
});

Route::resource('reklames', ReklameController::class)->only(['index', 'show']);
