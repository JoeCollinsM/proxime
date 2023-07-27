<?php

use App\Http\Controllers\Staff\CurrencyController;
use App\Http\Controllers\Staff\GeneralController;
use App\Http\Controllers\Staff\NotificationTemplateController;
use App\Http\Controllers\Staff\OrderController;
use App\Http\Controllers\Staff\ProductController;
use App\Http\Controllers\Staff\ReportController;
use App\Http\Controllers\Staff\RoleController;
use App\Http\Controllers\Staff\SettingsController;
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\Staff\UpdateController;
use App\Http\Controllers\Mpesa\MPESAController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::group(['middleware' => 'auth:staff'], function () {
    Route::group(['middleware' => 'able:manage_media', 'prefix' => 'media'], function () {
        \UniSharp\LaravelFilemanager\Lfm::routes();
    });
    Route::get('dashboard', [GeneralController::class, 'dashboard'])->name('dashboard');
    Route::get('profile', [GeneralController::class, 'profile'])->name('profile');
    Route::put('profile', [GeneralController::class, 'updateProfile'])->name('profile.update');

    Route::resource('transaction', 'TransactionController')->except(['show', 'destroy', 'edit', 'update'])->middleware('able:manage_transaction');
    Route::resource('withdraw', 'WithdrawController')->only(['index', 'update'])->middleware('able:manage_withdraw');

    Route::group(['prefix' => 'role', 'as' => 'role.', 'middleware' => 'able:manage_role'], function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::post('/', [RoleController::class, 'store'])->name('store');
        Route::put('{role}', [RoleController::class, 'update'])->name('update');
        Route::delete('{role}', [RoleController::class, 'destroy'])->name('destroy');
    });


    Route::group(['prefix' => 'staff', 'as' => 'staff.', 'middleware' => 'able:manage_staff'], function () {
        Route::get('/', [StaffController::class, 'index'])->name('index');
        Route::post('/', [StaffController::class, 'store'])->name('store');
        Route::put('{staff}', [StaffController::class, 'update'])->name('update');
        Route::delete('{staff}', [StaffController::class, 'destroy'])->name('destroy');
    });

    /*
     * Shop Management
     * */
    Route::group(['middleware' => 'able:manage_shop'], function () {
        Route::resource('shop-category', 'ShopCategoryController');
        Route::resource('shop', 'ShopController');
    });

    /*
     * Catalogs
     * */
    Route::group(['prefix' => 'catalog', 'as' => 'catalog.', 'middleware' => 'able:manage_catalog'], function () {
        Route::resource('review', 'ReviewController')->only(['index', 'destroy']);
        Route::resource('category', 'CategoryController');
        Route::group(['prefix' => 'attribute', 'as' => 'attribute.'], function () {
            Route::resource('term', 'TermController');
        });
        
        Route::resource('attribute', 'AttributeController')->except(['create', 'edit']);
        Route::resource('tag', 'TagController')->only(['store']);
        Route::group(['as' => 'product.', 'prefix' => 'product'], function () {
            Route::post('variations', [ProductController::class, 'generateAllPossibleVariation'])->name('variations');
            Route::post('import', [ProductController::class, 'import'])->name('import');
        });
        Route::resource('product', 'ProductController');
        Route::resource('coupon', 'CouponController');
        Route::put('order/status/{id?}', [OrderController::class, 'updateStatus'])->name('order.status');
        Route::put('order/{order}/refund', [OrderController::class, 'refund'])->name('order.refund');
        Route::put('order/{order}/commission', [OrderController::class, 'commission'])->name('order.commission');
        Route::put('order/{order}/action', [OrderController::class, 'postAction'])->name('order.action');
        Route::post('order/note/store', [OrderController::class, 'storeNote'])->name('order.note.store');
        Route::get('order/{order}/invoice/{type?}', [OrderController::class, 'invoice'])->name('order.invoice');
        Route::post('order/{order}/payment', [OrderController::class, 'paymentStore'])->name('order.payment');
        Route::resource('order', 'OrderController');
        Route::resource('user', 'UserController');
        Route::resource('delivery-man', 'DeliveryManController');
        Route::get('consignment/commission/{consignment}', 'ConsignmentController@commission')->name('consignment.commission');
        Route::resource('consignment', 'ConsignmentController')->only(['store']);
    });

    /*
     * Report Management
     * */
    Route::group(['prefix' => 'report', 'as' => 'report.', 'middleware' => 'able:manage_report'], function () {
        Route::get('sales-by-date/{download?}', [ReportController::class, 'salesByDate'])->name('sales-by-date');
        Route::get('sales-by-category/{download?}', [ReportController::class, 'salesByCategory'])->name('sales-by-category');
        Route::get('sales-by-product/{download?}', [ReportController::class, 'salesByProduct'])->name('sales-by-product');
    });

    /*
     * Settings
     * */
    Route::group(['prefix' => 'setting', 'as' => 'setting.', 'middleware' => 'able:manage_setting'], function () {
        Route::resource('banner', 'BannerController');
        Route::put('currency/refresh', [CurrencyController::class, 'refresh'])->name('currency.refresh');
        Route::resource('currency', 'CurrencyController');
        Route::resource('language', 'LanguageController');
        Route::resource('payment-method', 'PaymentMethodController')->only([
            'index', 'update'
        ]);
        Route::resource('shipping-method', 'ShippingMethodController');
        Route::resource('withdraw-method', 'WithdrawMethodController');

        Route::get('template/{type?}', [NotificationTemplateController::class, 'index'])->name('template.index');
        Route::get('template/{notificationTemplate}/edit', [NotificationTemplateController::class, 'edit'])->name('template.edit');
        Route::put('template/{notificationTemplate}', [NotificationTemplateController::class, 'update'])->name('template.update');

        Route::get('general', [SettingsController::class, 'generalSettings'])->name('general.index');
        Route::put('general', [SettingsController::class, 'updateGeneralSettings'])->name('general.update');
        Route::get('logo', [SettingsController::class, 'logoSettings'])->name('logo.index');
        Route::put('logo', [SettingsController::class, 'updateLogoSettings'])->name('logo.update');
        Route::get('email', [SettingsController::class, 'emailSettings'])->name('email.index');
        Route::put('email', [SettingsController::class, 'updateEmailSettings'])->name('email.update');
        Route::get('service', [SettingsController::class, 'serviceSettings'])->name('service.index');
        Route::put('service', [SettingsController::class, 'updateServiceSettings'])->name('service.update');
        Route::get('app', [SettingsController::class, 'appSettings'])->name('app.index');
        Route::put('app', [SettingsController::class, 'updateAppSettings'])->name('app.update');

        Route::group(['prefix' => 'application', 'as' => 'application.'], function () {
            Route::get('/', [UpdateController::class, 'index'])->name('update.index');
            Route::get('check-update', [UpdateController::class, 'checkUpdate'])->name('update.check');
            Route::get('make-backup', [UpdateController::class, 'makeBackup'])->name('update.backup');
            Route::get('download-file', [UpdateController::class, 'downloadFile'])->name('update.download');
            Route::get('update', [UpdateController::class, 'update'])->name('update.perform');
        });

        // mpesa routes
        Route::group(['prefix' => 'mpesa', 'as' => 'mpesa.'], function () {
            Route::get('/', [MPESAController::class, 'mpesaSettings'])->name('index');
            Route::post('get-token', [MPESAController::class, 'getAccessToken']);
            Route::post('register-urls', [MPESAController::class, 'registerURLS']);
            Route::post('simulate', [MPESAController::class, 'simulateTransaction']);
            Route::post('stkpush', [MPESAController::class, 'stkPush']);
            Route::post('simulateb2c', [MPESAController::class, 'b2cRequest']);
            Route::post('check-status', [MPESAController::class, 'transactionStatus']);
            Route::post('reversal', [MPESAController::class, 'reverseTransaction']);   
        });
    });
});
