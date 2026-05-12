<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PropertyApiController;
use App\Http\Controllers\Api\BranchApiController;
use App\Http\Controllers\Api\StaffApiController;
use App\Http\Controllers\Api\InspectionApiController;

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Properties
    Route::get('/properties',         [PropertyApiController::class, 'index']);
    Route::get('/properties/{id}',    [PropertyApiController::class, 'show']);
    Route::post('/properties',        [PropertyApiController::class, 'store']);
    Route::put('/properties/{id}',    [PropertyApiController::class, 'update']);
    Route::delete('/properties/{id}', [PropertyApiController::class, 'destroy']);

    // Branches
    Route::get('/branches',       [BranchApiController::class, 'index']);
    Route::get('/branches/{id}',  [BranchApiController::class, 'show']);

    // Staff
    Route::get('/staff',      [StaffApiController::class, 'index']);
    Route::get('/staff/{id}', [StaffApiController::class, 'show']);

    // Inspections
    Route::get('/inspections',  [InspectionApiController::class, 'index']);
    Route::post('/inspections', [InspectionApiController::class, 'store']);
});