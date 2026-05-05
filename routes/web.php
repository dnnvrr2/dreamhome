<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ClientController;

Route::get('/', function () {
    return redirect()->route('properties.index');
});

Route::resource('branches', BranchController::class);
Route::resource('owners', OwnerController::class);
Route::resource('properties', PropertyController::class);
Route::resource('clients', ClientController::class);