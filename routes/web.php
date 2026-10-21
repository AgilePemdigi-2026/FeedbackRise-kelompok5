<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::get('/register', [AuthController::class, 'showRegister'])
    ->name('register');

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
});

Route::get('/input-issue', function () {
    return view('page.input-issue');
})->name('input.issue');