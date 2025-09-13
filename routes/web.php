<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;
Route::get('/', function () {
    return redirect()->route('home');
});
Route::get('/home', [FormController::class, 'index'])->name('home');

Route::post('/formstore', [FormController::class, 'store'])->name('forms.formstore');
Route::get('/forms/{id}', [FormController::class, 'show'])->name('forms.show');
