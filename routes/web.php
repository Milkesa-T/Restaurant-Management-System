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

// Redirect /manager base URL → dashboard
Route::get('/manager', fn() => redirect('/dashboard'))->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Manager Section Routes
    Route::prefix('manager')->name('manager.')->group(function () {
        Route::get('/menu',      fn() => Inertia::render('Manager/Menu'))->name('menu');
        Route::get('/kds',       fn() => Inertia::render('Manager/Kds'))->name('kds');
        Route::get('/orders',    fn() => Inertia::render('Manager/Orders'))->name('orders');
        Route::get('/tables',    fn() => Inertia::render('Manager/Tables'))->name('tables');
        Route::get('/inventory', fn() => Inertia::render('Manager/Inventory'))->name('inventory');
        Route::get('/finance',   fn() => Inertia::render('Manager/Finance'))->name('finance');
        Route::get('/staffs',    fn() => Inertia::render('Manager/Staffs'))->name('staffs');
    });
});

require __DIR__.'/auth.php';
