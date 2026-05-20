<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LogDashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/logs', [LogDashboardController::class, 'index'])->name('logs.index');
Route::get('/generate-logs', [LogDashboardController::class, 'generate']);
Route::post('/logs/clear', [LogDashboardController::class, 'clear']);
Route::delete('/logs/bulk-delete', [LogDashboardController::class, 'bulkDelete']);
Route::get('/logs/export', [LogDashboardController::class, 'export']);
Route::get('/logs/export-csv', [LogDashboardController::class, 'exportCsv']);
Route::get('/logs/export-txt', [LogDashboardController::class, 'exportTxt']);
Route::get('/logs/counts', [LogDashboardController::class, 'getCounts']);