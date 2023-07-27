<?php

use App\Http\Controllers\API\DeliveryMan\Auth\ForgotPasswordController;
use App\Http\Controllers\API\DeliveryMan\Auth\LoginController;
use App\Http\Controllers\API\DeliveryMan\Auth\VerificationController;
use App\Http\Controllers\API\DeliveryMan\MiscController;
use App\Http\Controllers\API\DeliveryMan\OrderController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/config', [MiscController::class, 'configs']);
Route::post('login/social', [LoginController::class, 'socialLogin']);
Auth::routes(['reset' => false]);
Route::post('password/reset', [ForgotPasswordController::class, 'sendPasswordResetOtp']);
Route::put('password/reset', [ForgotPasswordController::class, 'resetPassword']);
Route::group(['middleware' => ['auth:delivery_man']], function () {
    Route::post('send-verification-email', [VerificationController::class, 'emailResend']);
    Route::post('send-verification-sms', [VerificationController::class, 'smsResend']);
    Route::post('verify-email-otp', [VerificationController::class, 'emailVerify']);
    Route::post('verify-sms-otp', [VerificationController::class, 'smsVerify']);
});

Route::group(['middleware' => ['auth:delivery_man', 'verified_proxime:delivery_man']], function () {
    Route::get('order', [OrderController::class, 'index']);
    Route::get('order/{consignment}', [OrderController::class, 'show']);
    Route::put('order/{consignment}', [OrderController::class, 'update']);

    Route::resource('withdraw', 'WithdrawController');

    Route::get('transaction', [MiscController::class, 'transaction']);
    Route::get('profile', [MiscController::class, 'profile']);
    Route::put('token', [MiscController::class, 'updateDeviceToken']);
    Route::post('profile', [MiscController::class, 'updateProfile']);
    Route::post('password', [MiscController::class, 'updatePassword']);
});
