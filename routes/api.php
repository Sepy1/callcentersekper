<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TicketApiController;
use App\Http\Controllers\Api\WhatsappController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\KonfirmasiWabaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Respond to CORS preflight for any API path if webserver forwards OPTIONS
Route::options('{any}', function () { return response()->noContent(); })->where('any', '.*');

// API endpoints for tickets
Route::post('/tickets', [TicketApiController::class, 'store']);
Route::get('/tickets/{id}', [TicketApiController::class, 'show']);
// Public categories list
Route::get('/categories', [CategoryApiController::class, 'index']);

// Update WABA by hp


// Protected WA endpoints: require Authorization: Bearer <token> and will be logged
Route::middleware(['api.token','api.request.log'])->group(function () {
    Route::get('/wa/templates', [WhatsappController::class, 'templates']);
    Route::post('/wa/send', [WhatsappController::class, 'send']);
    
    // Update WABA by hp
    Route::post('/konfirmasiwaba', [KonfirmasiWabaController::class, 'updateByHp']);
});
