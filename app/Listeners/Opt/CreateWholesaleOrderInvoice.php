<?php

namespace App\Listeners\Opt;

use App\Events\Opt\WholesaleOrderCreated;
use App\Jobs\Opt\CreateInvoiceDocument;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateWholesaleOrderInvoice implements ShouldQueue
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
     */
    public function handle(WholesaleOrderCreated $event)
    {
        if ($event->shouldQueue) {
            CreateInvoiceDocument::dispatch($event->order)->onConnection('database');
        } else {
            try {
                CreateInvoiceDocument::dispatch($event->order)->onConnection('sync');
            } catch (\Exception $exception) {
                \Log::error($exception);
            }
        }

    }
}
