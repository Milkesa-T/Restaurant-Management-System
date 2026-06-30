<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KitchenChefController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/kds', [KitchenChefController::class, 'kds'])->name('kds');
Route::get('/chef', [KitchenChefController::class, 'chef'])->name('chef');
Route::get('/customer', function () {
    return Inertia::render('CustomerPortal');
})->name('customer');
Route::get('/staff', function () {
    return Inertia::render('StaffPortal');
})->name('staff');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
