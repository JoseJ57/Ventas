<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticuloController;
use App\Http\Controllers\ClienteController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('articulos/form-data', [ArticuloController::class, 'formData']);
Route::apiResource('articulos', ArticuloController::class);
Route::apiResource('clientes', ClienteController::class);
