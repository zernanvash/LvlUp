<?php

use App\Http\Controllers\Api\ResumeGenerationController;
use Illuminate\Support\Facades\Route;

Route::post('/resume/generate', [ResumeGenerationController::class, 'store'])
    ->middleware('throttle:10,1');
