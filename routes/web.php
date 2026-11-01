<?php

use App\Http\Controllers\Student\CareerController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [CareerController::class, 'index'])->name('dashboard');
    Route::get('student/profile', [CareerController::class, 'edit'])->name('student.profile.edit');
    Route::put('student/profile', [CareerController::class, 'update'])->name('student.profile.update');
});

require __DIR__.'/settings.php';
