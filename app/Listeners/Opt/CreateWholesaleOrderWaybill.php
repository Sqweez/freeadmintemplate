<?php

namespace App\Listeners\Opt;

use App\Events\Opt\WholesaleOrderCreated;
use App\Jobs\Opt\CreateWaybillDocument;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateWholesaleOrderWaybill implements ShouldQueue
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
     * @param  WholesaleOrderCreated  $event
     * @return void
     * @throws \Exception
     */
    public function handle(WholesaleOrderCreated $event)
    {
        if ($event->shouldQueue) {
            CreateWaybillDocument::dispatch($event->order)->onConnection('database');
        } else {
            try {
                CreateWaybillDocument::dispatch($event->order)->onConnection('sync');
            } catch (\Exception $exception) {
                \Log::error($exception);
            }
        }
    }
}
