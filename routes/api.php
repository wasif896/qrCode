<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\QrCodeController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/register', [UserController::class,'register']);

Route::post('/login',[UserController::class,'login']);
Route::post('/forgotPassword', [UserController::class, 'forgotPassword']);
Route::post('/resetPassword', [UserController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/getUser',[UserController::class,'getUser']);
    Route::post('/updateUser', [UserController::class, 'updateUser']);
    Route::post('/addQrcode', [QrCodeController::class, 'addQrcode']);
    Route::get('/getQrcode', [QrCodeController::class, 'getQrcode']);
    Route::get('/scan-qr-code/{id}', [QrCodeController::class, 'scanQrCode']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/deleteAccount', [UserController::class, 'deleteAccount']);

});


Route::get('/login', function (Request $request) {
    return response()->json([
        'status' => false,
        'message' => 'Token is invalid',
    ], 401);
})->name("login");
