<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LogController;


// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware(['log.requests'])->group(function () {
    Route::post('/users', [UserController::class, 'createUser']);
    Route::get('/users', [UserController::class, 'listUsers']);
    Route::get('/logs', [LogController::class, 'index']);
    Route::get('/delete/{id}', [UserController::class, 'deleteUser']);

});
