<?php

namespace App\Listeners;

use App\Models\DeliveryMan;
use App\Events\WithdrawAccepted;
use App\Notifications\WithdrawAcceptedNotification;
use App\Models\Shop;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWithdrawAcceptedNotification
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
     * @param WithdrawAccepted $event
     * @return void
     */
    public function handle(WithdrawAccepted $event)
    {
        /* @var DeliveryMan|Shop $user */
        $user = $event->withdraw->user;
        try {
            $user->notify(new WithdrawAcceptedNotification($event->withdraw));
        } catch (\Exception $exception) {
        }
    }
}
