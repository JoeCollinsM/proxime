<?php

use App\Http\Controllers\MiscController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Mpesa\MPESAController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [MiscController::class, 'red']);
Route::get('order/{order}/invoice/{type?}', [MiscController::class, 'invoice'])->name('order.invoice');
Route::get('payment/{type}/{ref}', [PaymentController::class, 'detectPayment'])->name('payment');
Route::get('payment-success/{type}/{ref}', [PaymentController::class, 'successPayment'])->name('payment.success');
Route::get('payment-failed/{type}/{ref}', [PaymentController::class, 'failedPayment'])->name('payment.failed');

Route::post('payment/mobile/push', [MPESAController::class, 'proximeSTKPush']);
Route::post('payment/mobile/confirm', [MPESAController::class, 'proximeSTKStatusQuery']);


Route::get('mobilemoney', [MPESAController::class, 'load']);
// Route::post('register-urls', [MPESAController::class, 'registerURLS']);
// Route::post('simulate', [MPESAController::class, 'simulateTransaction']);
// Route::post('stkpush', [MPESAController::class, 'stkPush']);
// Route::post('simulateb2c', [MPESAController::class, 'b2cRequest']);
// Route::post('check-status', [MPESAController::class, 'transactionStatus']);
// Route::post('reversal', [MPESAController::class, 'reverseTransaction']);   