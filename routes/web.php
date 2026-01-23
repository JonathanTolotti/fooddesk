<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Cadastros (Categories, Products, Ingredients)
    Route::middleware('can:manage-products')->group(function () {
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories/filter', [CategoryController::class, 'filter'])->name('categories.filter');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        Route::patch('/categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
        Route::post('/categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');
        Route::get('/categories/{category}/history', [CategoryController::class, 'history'])->name('categories.history');
    });

    // Sistema (Users)
    Route::middleware('can:manage-users')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::post('users/filter', [UserController::class, 'filter'])->name('users.filter');
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::get('users/{user}/history', [UserController::class, 'history'])->name('users.history');
    });
});

require __DIR__.'/auth.php';
