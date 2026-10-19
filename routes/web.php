<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');