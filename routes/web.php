<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\LeaseController;
use App\Http\Controllers\ViewingController;
use App\Http\Controllers\InspectionController;

// Auth routes
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('branches', BranchController::class);
    Route::resource('owners', OwnerController::class);
    Route::resource('properties', PropertyController::class);
    Route::resource('staff', StaffController::class);
    Route::resource('clients', ClientController::class);
    Route::resource('leases', LeaseController::class);
    Route::resource('viewings', ViewingController::class);
    Route::resource('inspections', InspectionController::class);
});