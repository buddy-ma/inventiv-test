<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\CalculatorController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Calculator routes
Route::get('/calculator', [CalculatorController::class, 'index'])->name('calculator.index');
Route::post('/calculator/add-number', [CalculatorController::class, 'addNumber'])->name('calculator.addNumber');
Route::post('/calculator/clear-number', [CalculatorController::class, 'clearNumber'])->name('calculator.clearNumber');
Route::post('/calculator/clear-all', [CalculatorController::class, 'clearAll'])->name('calculator.clearAll');
Route::post('/calculator/set-operation', [CalculatorController::class, 'setOperation'])->name('calculator.setOperation');
Route::post('/calculator/calculate', [CalculatorController::class, 'calculate'])->name('calculator.calculate');
Route::get('/calculator/livewire', function () {
    return view('calculator.livewire');
})->name('calculator.livewire');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
