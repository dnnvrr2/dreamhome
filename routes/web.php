<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ViewingController;
use App\Http\Controllers\LeaseController;
use App\Http\Controllers\InspectionController;

Route::get('/', function () {
    return redirect()->route('properties.index');
});

Route::resource('branches', BranchController::class);
Route::resource('owners', OwnerController::class);
Route::resource('properties', PropertyController::class);
Route::resource('clients', ClientController::class);
Route::resource('staff', StaffController::class);
Route::resource('viewings', ViewingController::class);
Route::resource('leases', LeaseController::class);
Route::resource('inspections', InspectionController::class);