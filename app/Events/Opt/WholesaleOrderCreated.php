<?php

namespace App\Events\Opt;

use App\v2\Models\WholesaleOrder;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WholesaleOrderCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public WholesaleOrder $order;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(WholesaleOrder $order)
    {
        $this->order = $order;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
