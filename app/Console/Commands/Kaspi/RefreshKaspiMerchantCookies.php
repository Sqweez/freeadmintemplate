<?php

namespace App\Console\Commands\Kaspi;

use App\Service\v2\Kaspi\KaspiCookiesResolver;
use Illuminate\Console\Command;

class RefreshKaspiMerchantCookies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kaspi:refresh-cookies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Получает свежий набор куков для доступа к личному кабинету Каспи';

    private KaspiCookiesResolver $resolver;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(KaspiCookiesResolver $resolver)
    {
        parent::__construct();
        $this->resolver = $resolver;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->resolver->resolve();
        return 0;
    }
}
