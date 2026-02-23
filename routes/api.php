<?php

use App\Http\Controllers\ExpeditionController;
use Illuminate\Support\Facades\Route;
use App\Models\Expedition;
use App\Http\Requests\CreateExpeditionRequest;
use App\Http\Controllers\Auth\CouncilAuthController;
use App\Http\Controllers\Auth\KingdomAuthController;


//logins
Route::post('/kingdom/login', [KingdomAuthController::class, 'login']);
Route::post('/council/login', [CouncilAuthController::class, 'login']);

//register
Route::post('/kingdom/register', [KingdomAuthController::class, 'register']);
Route::post('/council/register', [CouncilAuthController::class, 'register']);

//rota pra criação das expedições
Route::post('/expeditions', [ExpeditionController::class, 'store']);

//rota pra decidir sobre a expedição que ja existe
Route::patch(`expeditions/{id}/decision`);