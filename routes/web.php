<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\DataManagementController;
use App\Http\Controllers\DashboardController;

Route::get('/', function() {
    return Auth::check() ? redirect('/dashboard') : redirect('/login');
});

Route::middleware('guest')->group(function() {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->middleware('web');
    
    // Change Password Routes
    Route::get('/change-password', [\App\Http\Controllers\Auth\ChangePasswordController::class, 'show'])->name('password.change.form');
    Route::post('/change-password', [\App\Http\Controllers\Auth\ChangePasswordController::class, 'update'])->name('password.change');
});



Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // User Management
    Route::get('/user-management', [UserController::class, 'index'])->name('user.management');
    Route::post('/user', [UserController::class, 'store'])->name('user.store');
    Route::put('/user/{user}/name', [UserController::class, 'updateName'])->name('user.update.name');
    Route::put('/user/{user}/group', [UserController::class, 'updateGroup'])->name('user.update.group');
    Route::post('/user/{user}/reset-password', [UserController::class, 'resetPassword'])->name('user.reset.password');
    Route::delete('/user/{user}', [UserController::class, 'destroy'])->name('user.destroy');
    Route::get('/api/users/{user}', [UserController::class, 'show']);
    Route::get('/api/groups', [UserController::class, 'getGroups']);
    
    // Group Management
    Route::get('/group-management', [GroupController::class, 'index'])->name('group.management');
    Route::post('/group', [GroupController::class, 'store'])->name('group.store');
    Route::put('/group/{group}/description', [GroupController::class, 'updateDescription'])->name('group.update.description');
    Route::delete('/group/{group}', [GroupController::class, 'destroy'])->name('group.destroy');
    Route::get('/api/groups/{groupName}', [GroupController::class, 'show']);
    
    // Data Management
    Route::get('/data-management', [DataManagementController::class, 'index'])->name('data.management');
    Route::get('/data-management/export', [DataManagementController::class, 'export'])->name('data.export');
    Route::delete('/api/data/{id}', [DataManagementController::class, 'destroy']);
    Route::get('/data-management/export-excel', [DataManagementController::class, 'exportExcel'])->name('data.export.excel');
});


