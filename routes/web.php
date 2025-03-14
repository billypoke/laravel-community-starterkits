<?php

use App\Http\Controllers\StarterkitController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
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

// Admin-only routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('app/tags', [TagController::class, 'index'])
        ->middleware('admin')
        ->name('tags.index');
    Route::post('app/tags', [TagController::class, 'store'])
        ->middleware('admin')
        ->name('tags.store');
    Route::put('app/tags/{tag}', [TagController::class, 'update'])
        ->middleware('admin')
        ->name('tags.update');
    Route::delete('app/tags/{tag}', [TagController::class, 'destroy'])
        ->middleware('admin')
        ->name('tags.destroy');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
