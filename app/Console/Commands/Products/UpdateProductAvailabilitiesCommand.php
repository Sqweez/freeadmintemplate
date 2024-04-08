<?php

namespace App\Console\Commands\Products;

use App\v2\Models\ProductAvailability;
use App\v2\Models\ProductSku;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateProductAvailabilitiesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:quantities';
    protected int $chunkSize = 100;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Синхронизирует текущее количество остатков товаров';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $start = microtime(true);
        $step = 0;
        $cacheLastUpdated = \Cache::get('PRODUCT_AVAILABILITIES_LAST_UPDATED_AT');
        ProductSku::query()
            ->whereHas('batches', function ($q) use ($cacheLastUpdated) {
                return $q
                    ->when($cacheLastUpdated, function ($subQ) use ($cacheLastUpdated) {
                        return $subQ->where('updated_at', '>=', Carbon::parse($cacheLastUpdated));
                    })
                    ->where('quantity', '>', 0);
            })
            ->with(['batches' => function ($query) {
                return $query->where('quantity', '>', 0)->where('store_id', '!=', -1);
            }])
            ->select(['id', 'product_id'])
            ->chunk($this->chunkSize, function ($products) use (&$step) {
                $step++;
                $this->line('Processing: ' . $this->chunkSize * $step);
                foreach ($products as $product) {
                    $product
                        ->batches
                        ->groupBy('store_id')
                        ->each(function ($batches, $store_id) use ($product) {
                            $quantity = $batches->sum('quantity');
                            ProductAvailability::updateOrCreate([
                                'product_sku_id' => $product->id,
                                'product_id' => $product->product_id,
                                'store_id' => $store_id,
                                'quantity' => $quantity,
                            ], []);
                        });
                }
            });
        \Cache::put('PRODUCT_AVAILABILITIES_LAST_UPDATED_AT', now());
        $end = microtime(true);
        $executionTime = round($end - $start, 2);
        $this->info('Время выполнения команды: ' . $executionTime . ' секунд');

    }
}
