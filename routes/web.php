<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/api/items', [ApiController::class, 'items'])->name('api.items.index');
Route::get('/api/items/{id}', [ApiController::class, 'show'])->name('api.items.show');

Route::middleware(['auth'])->group(function () {
    Route::get('/inventaris', [UserController::class, 'inventaris'])
        ->name('inventaris.index')
        ->middleware('check.role:user');

    Route::get('/inventaris/search', [UserController::class, 'search'])
        ->name('inventaris.search')
        ->middleware('check.role:user');

    Route::prefix('admin')->middleware('check.role:admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

        Route::get('/items/ajax', [AdminController::class, 'ajaxIndex'])->name('admin.items.ajax.index');
        Route::post('/items/ajax', [AdminController::class, 'ajaxStore'])->name('admin.items.ajax.store');
        Route::put('/items/ajax/{id}', [AdminController::class, 'ajaxUpdate'])->name('admin.items.ajax.update');
        Route::delete('/items/ajax/{id}', [AdminController::class, 'ajaxDestroy'])->name('admin.items.ajax.destroy');

        Route::get('/items/create', [AdminController::class, 'create'])->name('admin.items.create');
        Route::post('/items', [AdminController::class, 'store'])->name('admin.items.store');
        Route::get('/items/{id}/edit', [AdminController::class, 'edit'])->name('admin.items.edit');
        Route::put('/items/{id}', [AdminController::class, 'update'])->name('admin.items.update');
        Route::delete('/items/{id}', [AdminController::class, 'destroy'])->name('admin.items.destroy');

        Route::get('/transactions', [TransactionController::class, 'index'])->name('admin.transactions.index');
        Route::get('/transactions/create', [TransactionController::class, 'create'])->name('admin.transactions.create');
        Route::post('/transactions', [TransactionController::class, 'store'])->name('admin.transactions.store');
    });
});
