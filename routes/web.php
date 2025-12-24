<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Students;
use App\Livewire\StudentPriceManager;
use App\Livewire\LessonCreate; 

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/profile-edit', function() {
    return view('profile');
})->middleware(['auth'])->name('profile.edit');

Route::middleware(['auth'])->group(function () {
    // Сите три сега користат ист метод (view)
    Route::get('/students', function () {
        return view('students');
    })->name('students');

    Route::get('/student-prices', function () {
        return view('student-prices'); // Овој фајл штотуку го направи
    })->name('student-prices');

    Route::get('/lessons-log', function () {
        return view('lessons-log'); // Овој фајл штотуку го направи
    })->name('lessons-log');
});

require __DIR__.'/auth.php';