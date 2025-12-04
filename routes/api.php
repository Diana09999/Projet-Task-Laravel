<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', function (Request $request) {
        return $request->user();
    });
    Route::apiResource('projects', ProjectController::class);
    Route::post('projects/{id}/restore', [ProjectController::class, 'restore']);
    
    Route::apiResource('projects.tasks', TaskController::class);

    Route::post('/logout', [AuthController::class, 'logout']);
});
