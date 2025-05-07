<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\AuthController;

// Rute API untuk user
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);



// Rute API untuk movies
Route::middleware(['security_api', 'throttle:100,1'])->group(function () {
    Route::apiResource('movies', MovieController::class);
});
