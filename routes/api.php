<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TrashController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API Routes >> make the routes grouping into v1
Route::get('/trash', [TrashController::class, 'index']);
Route::get('/reports', [ReportController::class, 'index']);
Route::get('/reports/{id}', [ReportController::class, 'show']);
Route::get('/trash/{id}', [TrashController::class, 'show']);
Route::post('/trash', [TrashController::class, 'create']);
Route::get('/trash', [TrashController::class, 'index']);
Route::put('/trash/{id}', [TrashController::class, 'update']);
Route::delete('/trash/{id}', [TrashController::class, 'destroy']);
Route::post('/reports', [ReportController::class, 'create']);
Route::put('/reports/{id}', [ReportController::class, 'update']);
Route::delete('/reports/{id}', [ReportController::class, 'destroy']);
Route::put('/reports/{id}/status', [ReportController::class, 'updateStatus']);
Route::get('/reports/search', [ReportController::class, 'search']);
Route::get('/reports/filter', [ReportController::class, 'filterByStatus']);
Route::get('/reports/sort', [ReportController::class, 'sortByDate']);
Route::get('/reports/paginate', [ReportController::class, 'paginate']);