<?php

namespace App\Console\Commands\Kaspi;

use App\Actions\Kaspi\CreateKaspiPriceAction;
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

    protected function memory () {
        return 512;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('memory_limit', '512M');
        $kaspiEntities = KaspiEntity::query()->where('id', 1)->get();
        foreach ($kaspiEntities as $kaspiEntity) {
            $action = new CreateKaspiPriceAction($kaspiEntity);
            $action->handle();
            $this->line('OK');
        }
    }
}
