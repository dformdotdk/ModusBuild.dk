<?php

use App\Http\Controllers\Api\V1\HealthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/health', HealthController::class)->name('api.health');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
