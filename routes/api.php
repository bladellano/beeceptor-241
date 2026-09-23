<?php

use App\Http\Controllers\Api\EndpointController;
use App\Http\Controllers\Api\MockRuleController;
use App\Http\Controllers\Api\RequestLogController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::post('/endpoints', [EndpointController::class, 'store']);
    Route::get('/endpoints', [EndpointController::class, 'index']);
    Route::get('/endpoints/{endpoint}', [EndpointController::class, 'show']);
    Route::put('/endpoints/{endpoint}', [EndpointController::class, 'update']);
    Route::delete('/endpoints/{endpoint}', [EndpointController::class, 'destroy']);

    Route::post('/endpoints/{endpoint}/rules', [MockRuleController::class, 'store']);
    Route::get('/endpoints/{endpoint}/rules', [MockRuleController::class, 'index']);
    Route::put('/rules/{rule}', [MockRuleController::class, 'update']);
    Route::delete('/rules/{rule}', [MockRuleController::class, 'destroy']);

    Route::get('/endpoints/{endpoint}/requests', [RequestLogController::class, 'index']);
    Route::get('/requests/{requestLog}', [RequestLogController::class, 'show']);
});
