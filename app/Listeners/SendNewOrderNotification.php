<?php

namespace App\Listeners;

use App\Events\OrderPaymentConfirmed;
use App\Notifications\NewOrderNotification;
use App\Notifications\NewOrderNotificationToShop;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendNewOrderNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderPaymentConfirmed  $event
     * @return void
     */
    public function handle(OrderPaymentConfirmed $event)
    {
        try {
            $payment = $event->payment;
            /* @var Order $order */
            $order = $payment->order;
            if ($order->shop) {
                $order->shop->notify(new NewOrderNotificationToShop($order));
            }
            /* @var User $customer */
            $customer = $order->user;
            $customer->notify(new NewOrderNotification($order));
        } catch (\Exception $exception) {
            Log::error($exception->getTraceAsString());
        }
    }
}
