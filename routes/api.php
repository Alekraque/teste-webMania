<?php

use App\Http\Controllers\ExpeditionController;
use Illuminate\Support\Facades\Route;
use App\Models\Expedition;
use App\Http\Requests\CreateExpeditionRequest;


//rota pra criação das expedições
Route::post('/expeditions', [ExpeditionController::class, 'store']);

//rota pra decidir sobre a expedição que ja existe
Route::patch(`expeditions/{id}/decision`);