<?php

namespace App\Jobs\Opt;

use App\Service\Documents\InvoiceDocumentService;
use App\v2\Models\WholesaleOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateInvoiceDocument implements ShouldQueue
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
        $service = new InvoiceDocumentService($this->order);
        $path = $service->create();
        if ($path) {
            try {
                if ($this->order->invoice) {
                    \Storage::disk('public')->delete($this->order->invoice);
                }
            } catch (\Exception $exception) {

            } finally {
                $this->order->update(['invoice' => $path]);
            }
        }
    }
}
