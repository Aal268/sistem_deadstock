<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RestockAnalysisController;

Route::get('/', [DashboardController::class, 'index']);
Route::get('/analysis', [RestockAnalysisController::class, 'index']);
