<?php

namespace App\Console\Commands\Utils;

use Illuminate\Console\Command;

class TranslateProductDescriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translate:product-description';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Переводит описания товаров при помощи NodeJS';

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
        $jsCode = 'console.log("Hello from JavaScript!");';
        $command = 'node -e ' . escapeshellarg($jsCode);
        $output = [];
        exec($command, $output);

        foreach ($output as $line) {
            $this->info($line);
        }
    }
}
