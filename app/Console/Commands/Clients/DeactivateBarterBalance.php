<?php

namespace App\Console\Commands\Clients;

use App\v2\Models\BarterBalance;
use Illuminate\Console\Command;

class DeactivateBarterBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'barter:deactivate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отключает истекающие балансы';

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
     */
    public function handle()
    {
        BarterBalance::query()
            ->whereNotNull('active_until')
            ->whereDate('active_until', '<', today())
            ->update([
                'is_active' => false
            ]);
    }
}
