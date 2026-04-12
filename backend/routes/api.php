<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\SpotController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\SavedSpotController;
use App\Http\Controllers\SportsAndTagController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/registration', [RegistrationController::class, 'store']);
Route::post('/login', [AuthController::class, 'authenticate']);

Route::apiResource('/users', UserController::class)->only(['index', 'show']);

Route::apiResource('/spots', SpotController::class)->only(['index', 'show']);
Route::apiResource('/images', ImageController::class)->only(['index', 'show']);
Route::apiResource('/sports-and-tags', SportsAndTagController::class)->only(['index', 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/spots', SpotController::class)->only(['store', 'destroy']);
    Route::apiResource('/images', ImageController::class)->only(['store', 'destroy']);
    Route::apiResource('/saved-spots', SavedSpotController::class)->except(['update']);
});
