<?php

use App\Http\Controllers\API\AppController;
use App\Http\Controllers\API\Auth\ForgotPasswordController;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\VerificationController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\CatalogController;
use App\Http\Controllers\API\MiscController;
use App\Http\Controllers\API\WishListController;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mpesa\MPESAController;

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

Route::get('/lipia', [MPESAController::class, 'proximeSTKPush']);

Route::get('/config', [MiscController::class, 'configs']);
Route::get('/help', [MiscController::class, 'help']);
Route::match(['get', 'post'], 'payment-ipn-receiver/{type}/{ref}', [PaymentController::class, 'ipnReceiver'])->name('payment.ipn');

Route::post('login/social', [LoginController::class, 'socialLogin']);
Route::group(['as' => 'api.'], function () {
    Auth::routes(['reset' => false]);
});
Route::post('password/reset', [ForgotPasswordController::class, 'sendPasswordResetOtp']);
Route::put('password/reset', [ForgotPasswordController::class, 'resetPassword']);
Route::group(['middleware' => ['auth']], function () {
    Route::post('send-verification-email', [VerificationController::class, 'emailResend']);
    Route::post('send-verification-sms', [VerificationController::class, 'smsResend']);
    Route::post('verify-email-otp', [VerificationController::class, 'emailVerify']);
    Route::post('verify-sms-otp', [VerificationController::class, 'smsVerify']);
});


Route::get('languages', [MiscController::class, 'languages']);
Route::get('attributes', [CatalogController::class, 'attributes']);
Route::get('currencies', [MiscController::class, 'currencies']);

Route::group(['middleware' => ['auth', 'verified_proxime:api']], function () {
    Route::get('search-suggestions', [CatalogController::class, 'searchSuggestions']);
    Route::get('categories', [CatalogController::class, 'categories']);
    Route::get('shipping-methods', [CatalogController::class, 'shippingMethods']);
    Route::get('payment-methods', [CatalogController::class, 'paymentMethods']);
    Route::get('shop-categories', [CatalogController::class, 'shopCategories']);
    Route::get('shops', [CatalogController::class, 'shops']);
    Route::get('nearby-shops', [CatalogController::class, 'nearbyShops']);
    Route::post('follow-shop/{shop}', [CatalogController::class, 'followShop']);
    Route::post('unfollow-shop/{shop}', [CatalogController::class, 'unfollowShop']);
    Route::get('followed-shop', [CatalogController::class, 'followedShop']);
    Route::get('shop/{shop}', [CatalogController::class, 'shop']);
    Route::get('products', [CatalogController::class, 'products']);
    Route::get('product/{product}', [CatalogController::class, 'product']);
    Route::get('get-variant/{product}', [CatalogController::class, 'getVariantByAttrs']);

    
    Route::post('add-coupon', [CartController::class, 'attachCoupon']);
    Route::delete('remove-coupon', [CartController::class, 'detachCoupon']);
    Route::group(['prefix' => 'cart'], function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/', [CartController::class, 'store']);
        Route::put('/{cartItem}', [CartController::class, 'update']);
        Route::delete('/{cartItem}', [CartController::class, 'destroy']);
    });
    Route::group(['prefix' => 'wishlist'], function () {
        Route::get('/', [WishListController::class, 'index']);
        Route::post('/', [WishListController::class, 'store']);
        Route::put('/{cartItem}', [WishListController::class, 'update']);
        Route::delete('/{cartItem}', [WishListController::class, 'destroy']);
    });

    Route::resource('address', 'AddressController')->except(['create', 'edit']);
    Route::resource('order', 'OrderController')->except(['create', 'edit', 'destroy']);
    Route::resource('review', 'ReviewController')->only(['index', 'store']);

    Route::get('banner', [MiscController::class, 'banner']);
    Route::get('banner/{banner}', [MiscController::class, 'singleBanner']);

    Route::get('transaction', [MiscController::class, 'transaction']);
    Route::get('profile', [MiscController::class, 'profile']);
    Route::post('profile', [MiscController::class, 'updateProfile']);
    Route::put('notification', [MiscController::class, 'updatePushNotification']);
    Route::put('token', [MiscController::class, 'updateDeviceToken']);
    Route::post('password', [MiscController::class, 'updatePassword']);

    Route::get('notification/{type?}', [MiscController::class, 'notification']);
    Route::put('notification/{notification_id}', [MiscController::class, 'markAsRead']);
});

Route::post('validation', [MPESAResponsesController::class, 'validation']);
Route::post('confirmation', [MPESAResponsesController::class, 'confirmation']);
Route::post('stkpush', [MPESAResponsesController::class, 'stkPush']);
Route::post('b2ccallback', [MPESAResponsesController::class, 'b2cCallback']);
Route::post('transaction-status/result_url', [MPESAResponsesController::class, 'transactionStatusResponse']);
Route::post('reversal/result_url', [MPESAResponsesController::class, 'transactionReversal']);
