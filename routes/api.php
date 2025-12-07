<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Lomkit\Rest\Facades\Rest;
use App\Rest\Resources\ProjectResource;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/tasks/upload', [\App\Rest\Controllers\TasksController::class, 'uploadAttachment']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', function (Request $request) {
        return $request->user();
    });

    Rest::resource('projects', \App\Rest\Controllers\ProjectsController::class);
    Rest::resource('tasks', \App\Rest\Controllers\TasksController::class);
    Rest::resource('users', \App\Rest\Controllers\UsersController::class);

    Route::post('/logout', [AuthController::class, 'logout']);
});

\Lomkit\Rest\Facades\Rest::resource('users', \App\Rest\Controllers\UsersController::class);