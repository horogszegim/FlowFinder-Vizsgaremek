<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\SpotController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\SavedSpotController;
use App\Http\Controllers\SportsAndTagController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/registration', [RegistrationController::class, 'store']);
Route::post('/login', [AuthController::class, 'authenticate']);

Route::apiResource('/users', UserController::class)->only(['index', 'show']);

Route::apiResource('/spots', SpotController::class)->only(['index', 'show']);
Route::apiResource('/images', ImageController::class)->only(['index', 'show']);
Route::apiResource('/sports-and-tags', SportsAndTagController::class)->only(['index', 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return new UserResource($request->user());
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::patch('/spots/{spot}/images/order', [SpotController::class, 'updateImageOrder']);
    Route::apiResource('/spots', SpotController::class)->only(['store', 'update', 'destroy']);
    Route::apiResource('/images', ImageController::class)->only(['store', 'destroy']);
    Route::apiResource('/saved-spots', SavedSpotController::class)->except(['update']);
});
