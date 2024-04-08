<?php

namespace App\Listeners\Opt;

use App\Events\Opt\WholesaleOrderCreated;
use App\Service\Documents\WaybillDocumentService;

class CreateWholesaleOrderInvoice
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
        \Log::info('creating invoice with id ' . $event->order->id);
        $service = new WaybillDocumentService($event->order);
    }
}
