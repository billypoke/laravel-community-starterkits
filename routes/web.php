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

// Starterkit resource routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('app/starterkit/create', [StarterkitController::class, 'create'])
        ->name('starterkit.create');
    Route::post('/app/starterkit', [StarterkitController::class, 'store'])
        ->name('starterkit.store');
    Route::get('app/starterkit/{starterkit}/edit', [StarterkitController::class, 'edit'])
        ->name('starterkit.edit');
    Route::put('app/starterkit/{starterkit}', [StarterkitController::class, 'update'])
        ->name('starterkit.update');
    Route::delete('app/starterkit/{starterkit}', [StarterkitController::class, 'destroy'])
        ->name('starterkit.destroy');
});

Route::get('app/starterkits', [StarterkitController::class, 'index'])
    ->name('starterkit.index');
Route::get('/starterkits/load-more', [StarterkitController::class, 'loadMore']);
Route::get('/api/starterkits/load-more', [StarterkitController::class, 'loadMore']);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
