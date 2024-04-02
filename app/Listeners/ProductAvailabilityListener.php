<?php

namespace App\Listeners;

use Log;

class ProductAvailabilityListener
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
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        //
    }

    public function created($model)
    {
        $this->updateAvailabilities($model);
    }

    public function updated($model)
    {
        $this->updateAvailabilities($model);
    }

    private function updateAvailabilities($model)
    {
        Log::info('Обновление остатков для модели ' . get_class($model) . ' с ID ' . $model->id);
    }
}
