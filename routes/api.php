<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DataManagementController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group automatically.
|
*/

// Apply session middleware explicitly to auth routes
Route::middleware(['api', \Illuminate\Session\Middleware\StartSession::class])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/change-password', [\App\Http\Controllers\Api\ChangePasswordController::class, 'update']);

    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/validate-token', [AuthController::class, 'validateToken']);
    Route::post('/upload', [AuthController::class, 'uploadData']);
});

Route::get('/data', [DataController::class, 'index']);
Route::get('/data/verified-aggregated', [DataController::class, 'getVerifiedAggregated']);
Route::get('/data/coordinates', [DataController::class, 'findByCoordinates']);
Route::get('/data/unverified-count', [DataController::class, 'countUnverified']);
Route::get('/data/{id}', [DataController::class, 'show']);
Route::put('/data/spanduk-count/{id}', [DataManagementController::class, 'updateSpandukCount']);
