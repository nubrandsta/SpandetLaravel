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
    Route::get('/dashboard', function() {
    $query = \App\Models\Data::query();

    if ($search = request('search')) {
        $query->where('locality', 'like', "%{$search}%")
            ->orWhere('subLocality', 'like', "%{$search}%");
    }

    $data = $query->paginate(50);
    
    return view('dashboard', ['data' => $data]);
})->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});


