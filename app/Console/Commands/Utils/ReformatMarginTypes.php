<?php

namespace App\Console\Commands\Utils;

use App\v2\Models\Product;
use Illuminate\Console\Command;

class ReformatMarginTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'margin:reformat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

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
        $products = Product::query()
            ->with('sku')
            ->each(function (Product $product) {
                $this->line($product->product_name);
                $sku = $product->sku->first();
                if ($sku) {
                    $product->update([
                        'margin_type_id' => $sku->margin_type_id
                    ]);
                }
            });
    }
}
