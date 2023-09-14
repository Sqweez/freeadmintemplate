<?php

namespace App\Console\Commands\Utils;

use Carbon\Carbon;
use File;
use Illuminate\Console\Command;

class UnlinkOldPriceLists extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'price-list:unlink';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Удаляет устаревшие прайс-листы для экономии места на диске';

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
        $directory = getcwd() . '/storage/app/public/excel/waybills';
        try {
            $files = File::files($directory);
            foreach ($files as $file) {
                $createdAt = Carbon::createFromTimestamp($file->getCTime());
                $this->line($createdAt);
                if ($createdAt->diffInDays(now()) > 7) {
                   unlink($file);
                }
            }
        } catch (\Exception $exception) {
            $this->line($exception->getMessage());
        }
    }
}
