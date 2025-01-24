<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\AuthController;


// Routes publiques (pas besoin d'être connecté pour y accéder)
Route::post('login', [AuthController::class, 'login']);

// Routes protégées par l'authentification
Route::middleware(['auth:api'])->group(function () {
    Route::post('/users', [UserController::class, 'createUser']);
    Route::get('/users', [UserController::class, 'listUsers']);
    Route::get('/logs', [LogController::class, 'index']);
    Route::get('/delete/{id}', [UserController::class, 'deleteUser']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

