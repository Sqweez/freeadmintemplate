<?php

namespace App\Console\Commands\Kaspi;

use App\Service\Kaspi\KaspiOrdersApiService;
use Illuminate\Console\Command;

class RetrieveKaspiOrdersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kaspi:orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Получает заказы из каспи';

    private KaspiOrdersApiService $apiService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(KaspiOrdersApiService $apiService)
    {
        parent::__construct();
        $this->apiService = $apiService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $response = $this->apiService->getOrderById(443214829);
        $this->line(json_encode($response));
    }
}
