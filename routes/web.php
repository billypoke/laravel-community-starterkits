<?php

use App\Http\Controllers\StarterkitController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('app/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('app/starterkit/create', [StarterkitController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('starterkit.create');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
