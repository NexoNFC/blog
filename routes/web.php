<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ContentController as AdminContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NfcPointController as AdminNfcPointController;
use App\Http\Controllers\Admin\StatisticsController as AdminStatisticsController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NfcPointController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/contenidos/{slug}', [ContentController::class, 'show'])->name('contents.show');

Route::get('/nfc/{code}', [NfcPointController::class, 'show'])->name('nfc.show');

Route::middleware(['auth', 'active', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::middleware('permission:news.view')->group(function () {
        Route::get('/news', [AdminContentController::class, 'index'])->name('news.index');
        Route::get('/contenidos', [AdminContentController::class, 'index']);
    });

    Route::middleware('permission:news.create')->group(function () {
        Route::get('/news/create', [AdminContentController::class, 'create'])->name('news.create');
        Route::get('/contenidos/crear', [AdminContentController::class, 'create']);
        Route::post('/news/ingest', [AdminContentController::class, 'ingest'])->name('news.ingest');
    });

    Route::get('/news/{news}/edit', [AdminContentController::class, 'edit'])
        ->middleware('permission:news.update')
        ->name('news.edit');

    Route::patch('/news/{news}', [AdminContentController::class, 'update'])
        ->middleware('permission:news.update')
        ->name('news.update');

    Route::post('/news/{news}/rewrite', [AdminContentController::class, 'rewrite'])
        ->middleware('permission:news.update')
        ->name('news.rewrite');

    Route::post('/news/{news}/original', [AdminContentController::class, 'presentOriginal'])
        ->middleware('permission:news.update')
        ->name('news.original');

    Route::post('/news/{news}/publish', [AdminContentController::class, 'publish'])
        ->middleware('permission:news.publish')
        ->name('news.publish');

    Route::delete('/news/{news}', [AdminContentController::class, 'destroy'])
        ->middleware('permission:news.delete')
        ->name('news.destroy');

    Route::get('/categories', [AdminCategoryController::class, 'index'])
        ->middleware('permission:categories.view')
        ->name('categories.index');

    Route::middleware('permission:categories.create')->group(function () {
        Route::get('/categories/create', [AdminCategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    });

    Route::middleware('permission:categories.update')->group(function () {
        Route::get('/categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('categories.edit');
        Route::patch('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    });

    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])
        ->middleware('permission:categories.delete')
        ->name('categories.destroy');

    Route::get('/nfc', [AdminNfcPointController::class, 'index'])
        ->middleware('permission:nfc.view')
        ->name('nfc.index');

    Route::patch('/nfc/{nfcPoint}', [AdminNfcPointController::class, 'update'])
        ->middleware('permission:nfc.manage-content')
        ->name('nfc.update');

    Route::get('/statistics', [AdminStatisticsController::class, 'index'])
        ->middleware('role_or_permission:statistics.view|statistics.view-content|statistics.view-scans')
        ->name('statistics.index');

    Route::middleware('permission:users.view')->group(function () {
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    });

    Route::middleware('permission:users.create')->group(function () {
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    });

    Route::middleware('permission:users.update')->group(function () {
        Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    });

    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])
        ->middleware('permission:users.delete')
        ->name('users.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
