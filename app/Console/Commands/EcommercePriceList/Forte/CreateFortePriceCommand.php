<?php

namespace App\Console\Commands\EcommercePriceList\Forte;

use App\Builder\CreateKaspiActionBuilder;
use App\v2\Models\KaspiEntity;
use Exception;
use Illuminate\Console\Command;

class CreateFortePriceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forte:price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Генерирует прайс-лист для форте-маркета';

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
    protected function memory (): int
    {
        return 512;
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        ini_set('memory_limit', '512M');
        $kaspiEntity = KaspiEntity::whereKey(1)->first();
        $action = CreateKaspiActionBuilder::build($kaspiEntity, 'FORTE');
        $action->handle();
        $this->line('OK');
    }
}
