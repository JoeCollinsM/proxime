<?php

namespace App\Providers;

use App\Events\AssignedOrderToDeliveryMan;
use App\Events\OrderPaymentConfirmed;
use App\Events\OrderStatusUpdated;
use App\Events\TransactionAdded;
use App\Events\WithdrawAccepted;
use App\Listeners\ProximeInstalledEventListener;
use App\Listeners\ReduceStocksAndClearCart;
use App\Listeners\SendAssignedNotification;
use App\Listeners\SendNewOrderNotification;
use App\Listeners\SendNewTransactionNotification;
use App\Listeners\SendOrderUpdateNotification;
use App\Listeners\SendWithdrawAcceptedNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use RachidLaasri\LaravelInstaller\Events\LaravelInstallerFinished;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        OrderPaymentConfirmed::class => [
            ReduceStocksAndClearCart::class,
            SendNewOrderNotification::class
        ],
        OrderStatusUpdated::class => [
            SendOrderUpdateNotification::class
        ],
        AssignedOrderToDeliveryMan::class => [
            SendAssignedNotification::class
        ],
        LaravelInstallerFinished::class => [
            ProximeInstalledEventListener::class
        ],
        TransactionAdded::class => [
            SendNewTransactionNotification::class
        ],
        WithdrawAccepted::class => [
            SendWithdrawAcceptedNotification::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
