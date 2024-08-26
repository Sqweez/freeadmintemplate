<?php

namespace App\Console\Commands\EcommercePriceList\Halyk;

use App\Builder\CreateKaspiActionBuilder;
use App\v2\Models\KaspiEntity;
use Illuminate\Console\Command;

class CreateHalykExcelPriceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'halyk:price-excel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создает прайс-лист для Halyk Market в формате Excel';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function memory (): int
    {
        return 512;
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        ini_set('memory_limit', '512M');
        $kaspiEntity = KaspiEntity::whereKey(1)->first();
        $action = CreateKaspiActionBuilder::build($kaspiEntity, CreateKaspiActionBuilder::HALYK_EXCEL);
        $action->handle();
        $this->line('OK');
    }
}
