<?php

namespace App\Console\Commands\Utils;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class TranslateProductDescriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'python:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Переводит описания товаров при помощи Python';

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
        $process = new Process(['python', getcwd() . '/python/translate.py', 'Школа']);
        $process->setInput(NULL);
        $process->setEnv(['LANG' => 'en_US.UTF-8']);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        } else {
            $output_data = $process->getOutput();
            $this->line($output_data);
            $this->line('OK');
        }
    }
}
