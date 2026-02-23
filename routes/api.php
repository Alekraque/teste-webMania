<?php

use App\Http\Controllers\ExpeditionController;
use Illuminate\Support\Facades\Route;
use App\Models\Expedition;
use App\Http\Requests\CreateExpeditionRequest;
use App\Http\Controllers\Auth\CouncilAuthController;
use App\Http\Controllers\Auth\KingdomAuthController;
use App\Http\Controllers\ExpeditionDecisionController;


//pretected routes
Route::middleware('auth:council')->group(function () {
    Route::post('/council/register', [CouncilAuthController::class, 'register']);
});

Route::middleware(['auth:council'])->group(function () {
    Route::patch('/expeditions/{protocol}/decision', [ExpeditionDecisionController::class, 'decide']);
});

//consult expedition status
Route::middleware(['auth:kingdom,council'])->group(function () {
    Route::get('/expeditions/{protocol}/status', [ExpeditionController::class, 'status']);
});

//logins
Route::post('/kingdom/login', [KingdomAuthController::class, 'login']);
Route::post('/council/login', [CouncilAuthController::class, 'login']);

//register
Route::post('/kingdom/register', [KingdomAuthController::class, 'register']);
Route::post('/council/register', [CouncilAuthController::class, 'register']);

//create expeditions
Route::post('/expeditions', [ExpeditionController::class, 'store']);

//decide expeditions already exist
Route::patch('/expeditions/{protocol}/decision', [ExpeditionDecisionController::class, 'decide']);
