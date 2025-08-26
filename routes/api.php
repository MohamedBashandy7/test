<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\UsersController;
use App\Http\Controllers\Users\UsersProjectsController;

// Authentication routes
Route::post('/register', [UsersController::class, 'register']);
Route::post('/verify', [UsersController::class, 'VerifyAccount']);
Route::post('/login', [UsersController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UsersController::class, 'logout']);
    Route::apiResource('projects', UsersProjectsController::class);
});
