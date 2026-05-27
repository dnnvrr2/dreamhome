<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientRequestController;
use App\Http\Controllers\LeaseController;
use App\Http\Controllers\ViewingController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\PublicPropertyController;
use App\Http\Controllers\AdvertController;

// Public property browsing and client requests
Route::get('/properties-for-rent', [PublicPropertyController::class, 'index'])->name('public.properties');
Route::post('/client-requests', [ClientRequestController::class, 'store'])->name('client-requests.store');

// Auth routes
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin + Manager only
    Route::middleware('role:admin,manager')->group(function () {
        Route::resource('staff', StaffController::class);
        Route::resource('leases', LeaseController::class);
        Route::get('client-requests', [ClientRequestController::class, 'index'])->name('client-requests.index');
        Route::post('client-requests/{id}/approve', [ClientRequestController::class, 'approve'])->name('client-requests.approve');
        Route::post('client-requests/{id}/reject', [ClientRequestController::class, 'reject'])->name('client-requests.reject');
    });

    // Admin + Manager + Supervisor
    Route::middleware('role:admin,manager,supervisor')->group(function () {
        Route::resource('properties', PropertyController::class)->except(['show']);
        Route::resource('owners', OwnerController::class)->except(['show']);
        Route::resource('clients', ClientController::class)->except(['show']);
        Route::resource('viewings', ViewingController::class)->except(['show']);
        Route::resource('inspections', InspectionController::class)->except(['show']);
        Route::resource('adverts', AdvertController::class)->except(['show']);
    });

    // All roles
    Route::middleware('role:admin,manager,supervisor,staff')->group(function () {
        Route::resource('branches', BranchController::class)->except(['show']);
    });
});
