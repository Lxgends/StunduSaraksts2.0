<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KurssController;
use App\Http\Controllers\PasniedzejsController;
use App\Http\Controllers\KabinetsController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/kurss', [KurssController::class, 'index']);
Route::get('/pasniedzejs', [PasniedzejsController::class, 'index']);
Route::get('/kabinets', [KabinetsController::class, 'index']);