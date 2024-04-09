<?php

namespace App\Jobs\Opt;

use App\Service\Documents\WaybillDocumentService;
use App\v2\Models\WholesaleOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateWaybillDocument implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public WholesaleOrder $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(WholesaleOrder $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $service = new WaybillDocumentService($this->order);
        $path = $service->create();
        if ($path) {
            $this->order->update(['waybill' => $path]);
        }
    }
}
