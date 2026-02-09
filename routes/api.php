<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TicketApiController;
use App\Http\Controllers\Api\WhatsappController;

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

// API endpoints for tickets
Route::post('/tickets', [TicketApiController::class, 'store']);
Route::get('/tickets/{id}', [TicketApiController::class, 'show']);

// Protected WA endpoints: require Authorization: Bearer <token> and will be logged
Route::middleware(['api.token','api.request.log'])->group(function () {
    Route::get('/wa/templates', [WhatsappController::class, 'templates']);
    Route::post('/wa/send', [WhatsappController::class, 'send']);
    // Public-ish categories list for clients/admins (no auth by default)
    Route::get('/categories', [\App\Http\Controllers\Api\CategoryApiController::class, 'index']);
});
