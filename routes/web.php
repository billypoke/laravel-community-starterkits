<?php

use App\Http\Controllers\IndexBookmarkedStarterKitsController;
use App\Http\Controllers\StarterkitController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ToggleStarterkitBookmarkController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('starterkit.index'))->name('home');

Route::prefix('app')->group(function () {
    // Guest routes
    Route::get('starterkits', [StarterkitController::class, 'index'])
        ->name('starterkit.index');
    Route::get('starterkits/load-more', [StarterkitController::class, 'loadMore'])
        ->name('starterkit.load-more');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('dashboard', [StarterkitController::class, 'dashboard'])->name('dashboard');

        // Starterkit resource routes
        Route::resource('starterkit', StarterkitController::class)
            ->only([
                'create',
                'store',
                'edit',
                'update',
                'destroy',
            ])
            ->names('starterkit');

        Route::post('starterkit/{starterkit}/bookmark', ToggleStarterkitBookmarkController::class)
            ->name('starterkit.bookmark');

        Route::get('bookmarks', IndexBookmarkedStarterKitsController::class)
            ->name('starterkit.bookmarks');

        // Admin-only routes
        Route::middleware('admin')
            ->resource('tags', TagController::class)
            ->only([
                'index',
                'store',
                'update',
                'destroy',
            ])
            ->names('tags');
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
