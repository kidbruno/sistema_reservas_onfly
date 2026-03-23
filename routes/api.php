<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/teste', [UserController::class, 'teste']);
Route::post('/envio', [UserController::class, 'envio']);
Route::get('/users', [UserController::class, 'getUser']);
Route::get('/user/{id}', [UserController::class, 'getUserById']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
 