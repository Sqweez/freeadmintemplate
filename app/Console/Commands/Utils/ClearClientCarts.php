<?php

namespace App\Console\Commands\Utils;

use App\Cart;
use Illuminate\Console\Command;

class ClearClientCarts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:carts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Очищает клиентские корзины, созданные более 3 дней назад';

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
     * @throws \Exception
     */
    public function handle()
    {
        Cart::query()
            ->whereDate('created_at', '>=', now()->subDays(2))
            ->get()
            ->each(function (Cart $cart) {
                $cart->products()->delete();
                $cart->delete();
            });
    }
}
