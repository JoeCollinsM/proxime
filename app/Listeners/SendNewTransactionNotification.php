<?php

namespace App\Listeners;

use App\DeliveryMan;
use App\Events\TransactionAdded;
use App\Notifications\NewTransactionMade;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendNewTransactionNotification
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
     * @param TransactionAdded $event
     * @return void
     */
    public function handle(TransactionAdded $event)
    {
        $transaction = $event->transaction;
        /* @var User|DeliveryMan|Shop $user */
        $user = $transaction->user;
        try {
            $user->notify(new NewTransactionMade($transaction));
        } catch (\Exception $exception) {
        }
    }
}
