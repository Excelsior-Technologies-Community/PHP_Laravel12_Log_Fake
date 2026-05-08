<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogDashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/logs', [LogDashboardController::class, 'index']);
Route::get('/generate-logs', [LogDashboardController::class, 'generate']);
Route::post('/logs/clear', [LogDashboardController::class, 'clear']);
Route::get('/logs/export', [LogDashboardController::class, 'export']);
