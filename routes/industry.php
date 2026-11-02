<?php

use App\Http\Controllers\Industry\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('industry')->name('industry.')->group(function () {
    Route::get('/', [WorkspaceController::class, 'index'])->name('overview');
    Route::put('/company', [WorkspaceController::class, 'company'])->name('company');
    Route::post('/vacancies', [WorkspaceController::class, 'store'])->name('vacancies.store');
    Route::put('/vacancies/{vacancy}', [WorkspaceController::class, 'update'])->whereNumber('vacancy')->name('vacancies.update');
});
