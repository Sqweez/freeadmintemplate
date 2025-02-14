<?php

namespace App\Console\Commands\Utils;

use App\v2\Models\ProductSku;
use Illuminate\Console\Command;

class CacheProductSkuNameCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'utils:sku-names';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return mixed
     */
    public function handle()
    {
        $step = 0;
        ProductSku::query()
            ->with('attributes')
            ->with('product')
            ->with('product.attributes')
            ->chunk(100, function ($chunks) use (&$step) {
                $step++;
                $this->line("Processing entries: " . 100 * $step);
                $chunks->each(function (ProductSku $sku) {
                    $baseName = $sku->product->product_name;
                    $skuAttributes = $sku->attributes->pluck('attribute_value')->join(' ');
                    $productAttributes = $sku->product->attributes->pluck('attribute_value')->join(' ');
                    $skuName = trim(sprintf("%s %s %s", $baseName, $productAttributes, $skuAttributes));
                    $this->line($skuName);
                    $sku->sku_name = $skuName;
                    $sku->save();
                });
            });
    }
}
