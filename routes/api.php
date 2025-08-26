<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users\UsersController;
use App\Http\Controllers\Users\UsersProjectsController;
use App\Http\Controllers\Users\UsersTasksController;

// Authentication routes
Route::post('/register', [UsersController::class, 'register']);
Route::post('/verify', [UsersController::class, 'VerifyAccount']);
Route::post('/login', [UsersController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UsersController::class, 'logout']);
    Route::get('/projects/search', [UsersProjectsController::class, 'getAllProjects']);
    Route::apiResource('projects', UsersProjectsController::class);
    Route::apiResource('tasks', UsersTasksController::class);
    Route::patch('/tasks/{task}/status', [UsersTasksController::class, 'updateStatus']);
    Route::get('/projects/{project}/tasks', [UsersTasksController::class, 'getProjectTasks']);
    Route::get('/developers', [UsersController::class, 'getAllUsers']);
});