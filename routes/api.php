<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReklameController;
use App\Http\Controllers\IotController;
use App\Http\Controllers\IotSessionController;

Route::get('/ping', function () {
    return response()->json([
        'success' => true,
        'message' => 'API aktif'
    ]);
});

Route::prefix('iot')->group(function () {
    Route::post('/photo', [IotController::class, 'uploadPhoto']);
    Route::post('/location', [IotController::class, 'uploadLocation']);

    Route::post('/session/start', [IotSessionController::class, 'start']);
    Route::get('/session/current', [IotSessionController::class, 'current']);
    Route::post('/session/close', [IotSessionController::class, 'close']);
});

Route::post('/esp32/upload', [ReklameController::class, 'apiStore']);
