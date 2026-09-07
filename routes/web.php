<?php

use App\Http\Controllers\Admin\ContentController as AdminContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NfcPointController as AdminNfcPointController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NfcPointController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/contenidos/{slug}', [ContentController::class, 'show'])->name('contents.show');

Route::get('/nfc/{code}', [NfcPointController::class, 'show'])->name('nfc.show');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('/contenidos', [AdminContentController::class, 'index'])->name('contents.index');
    Route::get('/contenidos/crear', [AdminContentController::class, 'create'])->name('contents.create');
    Route::get('/nfc', [AdminNfcPointController::class, 'index'])->name('nfc.index');
});
