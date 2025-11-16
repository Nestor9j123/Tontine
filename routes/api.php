<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController as ApiClientController;
use App\Http\Controllers\Api\TontineController as ApiTontineController;
use App\Http\Controllers\Api\PaymentController as ApiPaymentController;
use App\Http\Controllers\Api\ProductController as ApiProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentification API (Sanctum tokens)
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Infos sur l'utilisateur connecté
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/logout-all', [AuthController::class, 'logoutAll']);

    // Ressources principales (lecture seule pour l'instant)
    Route::get('/clients', [ApiClientController::class, 'index']);
    Route::get('/clients/{client}', [ApiClientController::class, 'show']);

    Route::get('/tontines', [ApiTontineController::class, 'index']);
    Route::get('/tontines/{tontine}', [ApiTontineController::class, 'show']);

    Route::get('/payments', [ApiPaymentController::class, 'index']);
    Route::get('/payments/{payment}', [ApiPaymentController::class, 'show']);

    Route::get('/products', [ApiProductController::class, 'index']);
    Route::get('/products/{product}', [ApiProductController::class, 'show']);
});
