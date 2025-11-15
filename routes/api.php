<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ContactController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login',[AuthenticationController::class,'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',[AuthenticationController::class,'logout']);
    Route::apiResource('contacts', ContactController::class);
});
