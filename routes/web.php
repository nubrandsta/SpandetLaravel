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
                ->orWhere('subLocality', 'like', "%{$search}%")
                ->orWhere('locality', 'like', "%{$search}%")
                ->orWhere('subAdmin', 'like', "%{$search}%")
                ->orWhere('adminArea', 'like', "%{$search}%")
                ->orWhere('postalCode', 'like', "%{$search}%");
        });
    }

    $sortColumns = ['created_at', 'uploader', 'group', 'spandukCount', 'thoroughfare', 'subLocality', 'locality', 'subAdmin', 'adminArea', 'postalCode'];
    $sort = request('sort');
    $direction = request('direction');
    
    if ($sort && in_array($sort, $sortColumns)) {
        if ($direction === 'asc') {
            $query->orderBy($sort);
        } else {
            $query->orderByDesc($sort);
        }
    } else {
        // Default sorting by created_at desc
        $query->orderByDesc('created_at');
    }

    $data = $query->paginate(50)
        ->appends(request()->query());
    
    return view('dashboard', ['data' => $data]);
})->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});


