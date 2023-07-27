<?php

namespace App\Events;

use App\Models\Consignment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AssignedOrderToDeliveryMan
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $consignment;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Consignment $consignment)
    {
        $this->consignment = $consignment;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
