<?php

namespace App\Events\Opt;

use App\v2\Models\WholesaleOrder;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WholesaleOrderCreated implements ShouldQueue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public WholesaleOrder $order;
    public bool $shouldQueue;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(WholesaleOrder $order, bool $shouldQueue = true)
    {
        $this->order = $order;
        $this->shouldQueue = $shouldQueue;
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
