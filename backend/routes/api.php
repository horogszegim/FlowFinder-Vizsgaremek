<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post("/registration", [RegistrationController::class, "store"])->name("registration.store");

Route::post("/login", [AuthController::class, "authenticate"])->name("auth.authenticate");