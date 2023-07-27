<?php

namespace App\Listeners;

use App\Models\DeliveryMan;
use App\Events\AssignedOrderToDeliveryMan;
use App\Notifications\AssignedConsignment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendAssignedNotification
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
     * @param  AssignedOrderToDeliveryMan  $event
     * @return void
     */
    public function handle(AssignedOrderToDeliveryMan $event)
    {
        try {
            $consignment = $event->consignment;
            /* @var DeliveryMan $deliveryMan */
            $deliveryMan = $consignment->delivery_man;
            $deliveryMan->notify(new AssignedConsignment($consignment));
        } catch (\Exception $exception) {
            Log::error($exception->getTraceAsString());
        }
    }
}
