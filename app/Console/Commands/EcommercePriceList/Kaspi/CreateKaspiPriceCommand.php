<?php

namespace App\Console\Commands\EcommercePriceList\Kaspi;

use App\Builder\CreateKaspiActionBuilder;
use App\v2\Models\KaspiEntity;
use Illuminate\Console\Command;

class CreateKaspiPriceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kaspi:price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Генерирует прайс для каспи-магазинов';

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
        $kaspiEntities = KaspiEntity::query()->active()->get();
        foreach ($kaspiEntities as $kaspiEntity) {
            $this->line(sprintf('Генерируем прайс для %s', $kaspiEntity->name));
            $action = CreateKaspiActionBuilder::build($kaspiEntity, 'KASPI');
            $action->handle();
            $this->line('OK');
        }
    }
}
