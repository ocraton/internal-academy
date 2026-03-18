<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Employee;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::resource('workshops', Admin\WorkshopController::class);
    Route::get('statistics', [Admin\StatisticsController::class, 'index'])->name('statistics.index');
});

Route::prefix('workshops')->name('employee.workshops.')->middleware(['auth', 'verified', 'role:employee'])->group(function () {
    Route::get('/', [Employee\WorkshopController::class, 'index'])->name('index');
    Route::get('/{workshop}', [Employee\WorkshopController::class, 'show'])->name('show');
    Route::post('/{workshop}/enroll', [Employee\EnrollmentController::class, 'store'])->name('enroll');
    Route::delete('/{workshop}/enroll', [Employee\EnrollmentController::class, 'destroy'])->name('unenroll');
});

require __DIR__.'/auth.php';
