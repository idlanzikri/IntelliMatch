<?php

use App\Http\Controllers\DiscoveryController;
use App\Http\Controllers\Student\CareerController;
use App\Http\Controllers\Student\OpportunityController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');
Route::get('/careers', [DiscoveryController::class, 'careers'])->name('careers.index');
Route::get('/talent', [DiscoveryController::class, 'talent'])->name('talent.index');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [CareerController::class, 'index'])->name('dashboard');
    Route::get('student/profile', [CareerController::class, 'edit'])->name('student.profile.edit');
    Route::put('student/profile', [CareerController::class, 'update'])->name('student.profile.update');
});

require __DIR__.'/settings.php';
require __DIR__.'/admin.php';
require __DIR__.'/institution.php';

require __DIR__.'/industry.php';

Route::get('/opportunities', [OpportunityController::class, 'index'])->middleware(['auth', 'verified'])->name('opportunities');

Route::middleware(['auth', 'verified'])->controller(\App\Http\Controllers\JourneyController::class)->group(function () {
    Route::post('/opportunities/{vacancy}/apply', 'apply');
    Route::get('/student/applications', 'applications');
    Route::get('/student/career-paths', 'paths');
    Route::put('/student/career-paths', 'plan');
    Route::get('/industry/vacancies/{vacancy}/applicants', 'applicants');
    Route::put('/industry/vacancies/{vacancy}/applicants/{application}', 'status');
});
