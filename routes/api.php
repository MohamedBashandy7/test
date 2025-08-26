<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\UsersController;
use App\Http\Controllers\Users\UsersProjectsController;
use App\Http\Controllers\Users\UsersTasksController;
use App\Http\Controllers\Users\UsersAnalyticsController;

// Authentication routes
Route::post('/register', [UsersController::class, 'register']);
Route::post('/verify', [UsersController::class, 'VerifyAccount']);
Route::post('/login', [UsersController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UsersController::class, 'logout']);
    Route::get('/projects/search', [UsersProjectsController::class, 'getAllProjects']);
    Route::put('/tasks/{task}/status', [UsersTasksController::class, 'updateStatus']);
    Route::apiResource('tasks', UsersTasksController::class);
    Route::get('/projects/{project}/tasks', [UsersTasksController::class, 'getProjectTasks']);
    Route::post('/projects/{project}/status', [UsersProjectsController::class, 'updateStatus']);
    Route::get('/projects/pending', [UsersProjectsController::class, 'getAllPendingProjects']);
    Route::apiResource('projects', UsersProjectsController::class);
    Route::get('/developers', [UsersController::class, 'getAllUsers']);
    Route::get('/stats', [UsersAnalyticsController::class, 'stats']);
});
