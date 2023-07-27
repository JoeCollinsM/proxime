<?php

namespace App\Listeners;

use App\Events\OrderStatusUpdated;
use App\Notifications\OrderUpdated;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendOrderUpdateNotification
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
     * @param  OrderStatusUpdated  $event
     * @return void
     */
    public function handle(OrderStatusUpdated $event)
    {
        try {
            $from = $event->from;
            $to = $event->to;
            $order = $event->order;
            /* @var User $customer */
            $customer = $order->user;
            if ($to == 0) {
                $status = 'pending';
            } elseif ($to == 1) {
                $status = 'processing';
            } elseif ($to == 2) {
                $status = 'on the way';
            } elseif ($to == 3) {
                $status = 'delivered';
            } elseif ($to == 4) {
                $status = 'hold';
            } elseif ($to == 5) {
                $status = 'canceled';
            } else {
                $status = 'unknown';
            }
            $customer->notify(new OrderUpdated($order, $status));
        } catch (\Exception $exception) {
            Log::error($exception->getTraceAsString());
        }
    }
}
