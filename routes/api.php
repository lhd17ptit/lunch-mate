<?php

use App\Http\Controllers\PayOsController;
use App\Http\Controllers\Admin\MicrosoftAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('payos')->name('payos.')->group(function () {
    Route::post('/webhook', [PayOsController::class, 'handleWebhook'])->name('handle-webhook');
});

Route::prefix('microsoft')->name('microsoft.')->group(function () {
    Route::get('/callback', [MicrosoftAuthController::class, 'callback'])->name('microsoft-callback');
});