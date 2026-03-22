<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SmsController;
use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\CheckApiToken;

Route::post('/users-create', [UserController::class, 'store']);
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware([CheckApiToken::class])->group(function () {
    Route::post('/send-sms-by-dlt', [SmsController::class, 'sendByDlt']);
    Route::get('/get-user-data', [UserController::class, 'getUserData']);
    Route::get('/get-user-ballance', [UserController::class, 'getUserBallance']);
    Route::patch('/get-user-ballance-add', [UserController::class, 'addUserBallance']);
});

