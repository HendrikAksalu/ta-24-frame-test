<?php

use App\Http\Controllers\Api\FriendFavoriteSubjectsController;
use App\Http\Controllers\Api\NflRookieController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/friend-favorite-subjects', FriendFavoriteSubjectsController::class);

Route::get('/rookies', [NflRookieController::class, 'index']);
Route::get('/rookies/{id}', [NflRookieController::class, 'show']);

Route::get('/nfl-rookies', [NflRookieController::class, 'index']);
Route::get('/nfl-rookies/{id}', [NflRookieController::class, 'show']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/rookies', [NflRookieController::class, 'store']);
    Route::put('/rookies/{id}', [NflRookieController::class, 'update']);
    Route::delete('/rookies/{id}', [NflRookieController::class, 'destroy']);

    Route::post('/nfl-rookies', [NflRookieController::class, 'store']);
    Route::put('/nfl-rookies/{id}', [NflRookieController::class, 'update']);
    Route::delete('/nfl-rookies/{id}', [NflRookieController::class, 'destroy']);
});

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);
