<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;

Route::get('/dashboard', [ProjectController::class, 'index']);
Route::get('/new', [ProjectController::class, 'create']);
Route::post('/projects', [ProjectController::class, 'store']);


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



