<?php

use App\Http\Controllers\Api\V2\ReportControllerV2;
use App\Http\Controllers\Api\V2\TrashController as TrashControllerV2;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TrashController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

// API Routes >> make the routes grouping into v1
Route::group(['middleware' => ['api', 'auth:api', 'jwt'], 'prefix' => 'v1'], function ($router) {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', [AuthController::class, 'profile']);

    Route::get('trash', [TrashController::class, 'index']);
    Route::get('trash/{id}', [TrashController::class, 'show']);
    Route::post('trash', [TrashController::class, 'create']);

    Route::get('reports', [ReportController::class, 'index']);
    Route::get('reports/paginated', [ReportController::class, 'indexPaginated']);
    Route::get('reports/{id}', [ReportController::class, 'show']);
    Route::post('reports', [ReportController::class, 'create']);
    Route::put('reports/{id}', [ReportController::class, 'updateReport']);
    Route::put('reports/{id}/status', [ReportController::class, 'updateStatus']);
    Route::delete('reports/{id}', [ReportController::class, 'destroy']);
    Route::post('reports/search', [ReportController::class, 'search']);
    Route::post('reports/filter', [ReportController::class, 'filterByStatus']);
    Route::get('reports/paginated', [ReportController::class, 'indexPaginated']);
    Route::get('reports/{id}/trash', [ReportController::class, 'trashPerReport']);
}); // Proteksi route

// API Routes V2
Route::group(['middleware' => ['api', 'auth:api', 'jwt'], 'prefix' => 'v2'], function ($router) {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', [AuthController::class, 'profile']);

    Route::get('trash', [TrashControllerV2::class, 'index']);
    Route::get('trash/{id}', [TrashControllerV2::class, 'show']);
    Route::post('trash', [TrashControllerV2::class, 'create']);

    Route::get('reports', [ReportControllerV2::class, 'index']);
    Route::get('reports/paginated', [ReportControllerV2::class, 'indexPaginated']);
    Route::get('reports/{id}', [ReportControllerV2::class, 'show']);
    Route::post('reports', [ReportControllerV2::class, 'create']);
    Route::put('reports/{id}', [ReportControllerV2::class, 'updateReport']);
    Route::put('reports/{id}/status', [ReportControllerV2::class, 'updateStatus']);
    Route::delete('reports/{id}', [ReportControllerV2::class, 'destroy']);
    Route::post('reports/search', [ReportControllerV2::class, 'search']);
    Route::post('reports/filter', [ReportControllerV2::class, 'filterByStatus']);
    Route::get('reports/paginated', [ReportControllerV2::class, 'indexPaginated']);
    Route::get('reports/{id}/trash', [ReportControllerV2::class, 'trashPerReport']);
});
