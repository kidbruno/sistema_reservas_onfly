<?php

use App\Http\Controllers\TripController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/user', [UserController::class, 'InsertUser']);
Route::get('/users', [UserController::class, 'getUser']);
Route::get('/user/{id}', [UserController::class, 'getUserById']);
Route::delete('/user/delete/{id}', [UserController::class, 'deleteUser']);

Route::post('/trip', [TripController::class, 'travelRequest']);
Route::get('/trips', [TripController::class, 'getAllTravels']);
Route::get('/trip/{id}', [TripController::class, 'getTravelById']);
Route::patch('/trip/{id}/status', [TripController::class, 'updateStatusTravel']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
 