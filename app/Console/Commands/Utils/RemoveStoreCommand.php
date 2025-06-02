<?php

namespace App\Console\Commands\Utils;

use App\ProductBatch;
use App\Store;
use Illuminate\Console\Command;

class RemoveStoreCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'store:remove {store_id : ID магазина для удаления}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Удаляет магазин из базы данных и удаляет все связанные с ним данные.';

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
    public function handle(): mixed
    {
        $store = Store::find($this->argument('store_id'));
        if (!$store) {
            return 1;
        }
        ProductBatch::query()
            ->where('store_id', $store->id)
            ->update(['quantity' => 0]);

        $store->delete();

        return 0;
    }
}
