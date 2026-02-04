<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});
Route::get('/dashboard', [DashboardController::class, 'dashboard']);

Route::get('/profile', function () {
    return view('profile');
});

Route::get('/skill-tree', function () {
    return view('skill-tree');
});



