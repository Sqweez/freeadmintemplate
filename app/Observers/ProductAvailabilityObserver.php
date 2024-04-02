<?php

namespace App\Observers;

use App\ProductBatch;

class ProductAvailabilityObserver
{
    /**
     * Handle the product batch "created" event.
     *
     * @param  \App\ProductBatch  $productBatch
     * @return void
     */
    public function created(ProductBatch $productBatch)
    {
        $this->updateAvailabilities($productBatch);
    }

    private function updateAvailabilities(ProductBatch $model)
    {
        \Log::info('Обновление остатков для модели ' . get_class($model) . ' с ID ' . $model->id . '. Текущий остаток ' . $model->quantity);
    }

    /**
     * Handle the product batch "updated" event.
     *
     * @param  \App\ProductBatch  $productBatch
     * @return void
     */
    public function updated(ProductBatch $productBatch)
    {
        $this->updateAvailabilities($productBatch);
    }

    /**
     * Handle the product batch "deleted" event.
     *
     * @param  \App\ProductBatch  $productBatch
     * @return void
     */
    public function deleted(ProductBatch $productBatch)
    {
        //
    }

    /**
     * Handle the product batch "restored" event.
     *
     * @param  \App\ProductBatch  $productBatch
     * @return void
     */
    public function restored(ProductBatch $productBatch)
    {
        //
    }

    /**
     * Handle the product batch "force deleted" event.
     *
     * @param  \App\ProductBatch  $productBatch
     * @return void
     */
    public function forceDeleted(ProductBatch $productBatch)
    {
        //
    }
}
