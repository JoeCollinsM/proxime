<?php

namespace App\Listeners;

use App\Events\OrderPaymentConfirmed;
use App\Helpers\API\Context;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class ReduceStocksAndClearCart
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
     * @param OrderPaymentConfirmed $event
     * @return void
     */
    public function handle(OrderPaymentConfirmed $event)
    {
        $payment = $event->payment;
        /* @var Order $order */
        $order = $payment->order;
        /* @var User $customer */
        $customer = $order->user;
        DB::beginTransaction();
        try {
            $order->reduceStocks();
            $customer->carts()->where('type', 'cart')->delete();
        } catch (\Exception $exception) {
            DB::rollBack();
        }
        DB::commit();
    }
}
