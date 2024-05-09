<?php

namespace App\Console;

use App\Console\Commands\Clients\CollectPlatinumClientsInformation;
use App\Console\Commands\Clients\DeactivateBarterBalance;
use App\Console\Commands\EcommercePriceList\Forte\CreateFortePriceCommand;
use App\Console\Commands\EcommercePriceList\Kaspi\CreateKaspiPriceCommand;
use App\Console\Commands\Products\UpdateProductAvailabilitiesCommand;
use App\Console\Commands\Trainers\CollectCashback;
use App\Console\Commands\Utils\ClearClientCarts;
use App\Console\Commands\Utils\ReformatMarginTypes;
use App\Console\Commands\Utils\UnlinkOldPriceLists;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(CollectPlatinumClientsInformation::class)
            ->monthlyOn(6, '10:00');
        $schedule->command(CollectPlatinumClientsInformation::class)
            ->monthlyOn(23, '10:00');
        $schedule->command(ClearClientCarts::class)
            ->hourly();
        $schedule->command(DeactivateBarterBalance::class)
            ->dailyAt('01:00');
        $schedule->command(UnlinkOldPriceLists::class)
            ->dailyAt('00:00');
        $schedule->command(ReformatMarginTypes::class)
            ->hourly();
        $schedule->command(CollectCashback::class)
            ->weeklyOn(7, '10:00');
        $schedule->command(CreateKaspiPriceCommand::class)
            ->everyThirtyMinutes();
        $schedule->command(CreateFortePriceCommand::class)
            ->everyThirtyMinutes();
        $schedule->command(UpdateProductAvailabilitiesCommand::class)
            ->everyMinute()->withoutOverlapping();
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    /**
     * Get the timezone that should be used by default for scheduled events.
     *
     * @return \DateTimeZone|string|null
     */
    protected function scheduleTimezone()
    {
        return 'Asia/Almaty';
    }
}
