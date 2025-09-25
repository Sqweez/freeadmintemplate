<?php

namespace App\Console\Commands\Storage;

use Illuminate\Console\Command;

class DeleteUnusedProductImageFiles extends Command
{
    
    protected $signature = 'utils-storage:delete-unused-product-images';
    
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        
    }
}
