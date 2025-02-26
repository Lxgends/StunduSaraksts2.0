<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KurssController;
use App\Http\Controllers\PasniedzejsController;
use App\Http\Controllers\KabinetsController;
use App\Http\Controllers\LaiksController;
use App\Http\Controllers\IeplanotStunduController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/ieplanotas-stundas', [IeplanotStunduController::class, 'index']);
Route::get('/laiks', [LaiksController::class, 'index']);


// Header routes
Route::get('/kurss', [KurssController::class, 'index']);
Route::get('/pasniedzejs', [PasniedzejsController::class, 'index']);
Route::get('/kabinets', [KabinetsController::class, 'index']);