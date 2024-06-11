<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\ApiDocController;
use App\Http\Controllers\AuthController;

// Rotas públicas
Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

Route::post('/usuario/cadastrar', [UserController::class, 'cadastrarUsuario']);
Route::get('/api/docs', [ApiDocController::class, 'docs']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Rotas protegidas que requerem autenticação com Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::post('/transfer', [TransferController::class, 'transfer']);
});
