<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SmsController;
use App\Http\Middleware\CheckApiToken;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware([CheckApiToken::class])->group(function () {
    Route::post('/send-sms-by-dlt', [SmsController::class, 'sendByDlt']);
});

