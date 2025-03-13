<?php

use App\Http\Controllers\StarterkitController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (Auth::check()) {
        return Inertia::render('Welcome');
    }
    return redirect()->route('starterkit.index');
})->name('home');

Route::get('app/dashboard', [StarterkitController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('app/starterkit/create', [StarterkitController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('starterkit.create');

Route::post('/app/starterkit', [StarterkitController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('starterkit.store');

Route::get('app/starterkits', [StarterkitController::class, 'index'])
    ->name('starterkit.index');

Route::get('/starterkits/load-more', [StarterkitController::class, 'loadMore']);

Route::get('/api/starterkits/load-more', [StarterkitController::class, 'loadMore']);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
