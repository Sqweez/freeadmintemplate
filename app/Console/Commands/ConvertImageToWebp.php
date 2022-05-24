<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ConvertImageToWebp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:webp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert every image to webp format';

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
    public function handle()
    {
        $files = $this->retrieveNonWebpImages();
    }

    public function retrieveNonWebpImages(): array {
        $files = \Storage::disk('public')->files('products');
        print_r($files);
        return $files;
    }
}
