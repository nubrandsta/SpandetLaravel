<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function() {
    return Auth::check() ? redirect('/dashboard') : redirect('/login');
});

Route::middleware('guest')->group(function() {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->middleware('web');
});



Route::middleware('auth')->group(function() {
    Route::get('/dashboard', function() { return view('dashboard'); });
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});


