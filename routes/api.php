<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Tymon\JWTAuth\Facades\JWTAuth;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:api')->post('logout', [AuthController::class, 'logout']);
Route::middleware('auth:api')->get('/user', function () {
    return JWTAuth::user();
});
