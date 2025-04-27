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
        $query->where(function($q) use ($search) {
            $q->where('uploader', 'like', "%{$search}%")
                ->orWhere('group', 'like', "%{$search}%")
                ->orWhere('spandukCount', 'like', "%{$search}%")
                ->orWhere('thoroughfare', 'like', "%{$search}%")
                ->orWhere('sublocality', 'like', "%{$search}%")
                ->orWhere('locality', 'like', "%{$search}%")
                ->orWhere('subadmin', 'like', "%{$search}%")
                ->orWhere('adminArea', 'like', "%{$search}%")
                ->orWhere('postalcode', 'like', "%{$search}%");
        });
    }

    $data = $query->paginate(50);
    
    return view('dashboard', ['data' => $data]);
})->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});


