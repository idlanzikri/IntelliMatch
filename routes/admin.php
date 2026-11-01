<?php

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('overview');
    Route::get('/students', [AdminController::class, 'students'])->name('students');
    Route::get('/careers', [AdminController::class, 'careers'])->name('careers');
    Route::post('/careers', [AdminController::class, 'store'])->name('careers.store');
    Route::put('/careers/{career}', [AdminController::class, 'update'])->name('careers.update');
});
