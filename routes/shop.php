<?php

use App\Http\Controllers\Shop\GeneralController;
use App\Http\Controllers\Shop\OrderController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\ReportController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::group(['middleware' => 'auth:shop'], function () {
    Route::group(['prefix' => 'media'], function () {
        \UniSharp\LaravelFilemanager\Lfm::routes();
    });
    Route::get('dashboard', [GeneralController::class, 'dashboard'])->name('dashboard');
    Route::get('profile', [GeneralController::class, 'profile'])->name('profile');
    Route::put('profile', [GeneralController::class, 'updateProfile'])->name('profile.update');

    Route::get('transaction', 'TransactionController@index')->name('transaction.index');
    Route::resource('withdraw', 'WithdrawController')->only(['index', 'create', 'store']);

    /*
     * Catalogs
     * */
    Route::group(['prefix' => 'catalog', 'as' => 'catalog.'], function () {
        Route::resource('tag', 'TagController')->only(['store']);
        Route::group(['as' => 'product.', 'prefix' => 'product'], function () {
            Route::post('variations', [ProductController::class, 'generateAllPossibleVariation'])->name('variations');
            Route::post('import', [ProductController::class, 'import'])->name('import');
        });
        Route::resource('product', 'ProductController');
        Route::put('order/status/{id?}', [OrderController::class, 'updateStatus'])->name('order.status');
        Route::resource('order', 'OrderController')->only(['index', 'show']);
    });

    /*
     * Report Management
     * */
    Route::group(['prefix' => 'report', 'as' => 'report.'], function () {
        Route::get('sales-by-date/{download?}', [ReportController::class, 'salesByDate'])->name('sales-by-date');
        Route::get('sales-by-product/{download?}', [ReportController::class, 'salesByProduct'])->name('sales-by-product');
    });
});