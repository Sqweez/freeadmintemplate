<?php

namespace App\Listeners\Opt;

use App\Events\Opt\WholesaleOrderCreated;
use App\Service\Documents\WaybillDocumentService;

class CreateWholesaleOrderWaybill
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
        try {
            $service = new WaybillDocumentService($event->order);
            $path = $service->create();
            if ($path) {
                $event->order->update(['waybill' => $path]);
            }
        } catch (\Exception $exception) {
            \Log::error($exception);
        }
    }
}
