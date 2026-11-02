<?php

use App\Http\Controllers\Student\CareerController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');
Route::get('/careers', [\App\Http\Controllers\DiscoveryController::class, 'careers'])->name('careers.index');
Route::get('/talent', [\App\Http\Controllers\DiscoveryController::class, 'talent'])->name('talent.index');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [CareerController::class, 'index'])->name('dashboard');
    Route::get('student/profile', [CareerController::class, 'edit'])->name('student.profile.edit');
    Route::put('student/profile', [CareerController::class, 'update'])->name('student.profile.update');
});

require __DIR__.'/settings.php';
require __DIR__.'/admin.php';
require __DIR__.'/institution.php';
