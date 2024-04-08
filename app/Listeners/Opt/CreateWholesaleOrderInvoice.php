<?php

namespace App\Listeners\Opt;

use App\Events\Opt\WholesaleOrderCreated;
use App\Service\Documents\InvoiceDocumentService;

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
        try {
            $service = new InvoiceDocumentService($event->order);
            $path = $service->create();
            if ($path) {
                $event->order->update(['invoice' => $path]);
            }
        } catch (\Exception $exception) {
            \Log::error($exception);
        }
    }
}
