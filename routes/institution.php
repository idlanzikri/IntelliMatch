<?php

use App\Http\Controllers\Institution\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('institution')->name('institution.')->group(function () {
    Route::get('/', [WorkspaceController::class, 'index'])->name('overview');
    Route::get('/students/{student}', [WorkspaceController::class, 'show'])->whereNumber('student')->name('students.show');
});
