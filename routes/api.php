<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Lomkit\Rest\Facades\Rest;
use App\Rest\Resources\ProjectResource;
use App\Rest\Resources\TaskResource;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

\Lomkit\Rest\Rest::resource('projects', ProjectResource::class);
\Lomkit\Rest\Rest::resource('tasks', TaskResource::class);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});
