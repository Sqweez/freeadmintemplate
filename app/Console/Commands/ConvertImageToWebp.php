<?php

namespace App\Console\Commands;

use App\ProductImage;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

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
        $images = $this->retrieveNonWebpImages();
        $images->each(function ($image, $k) {
            $this->convertImage($image, $k);
        });
    }

    private function retrieveNonWebpImages(): Collection {
        $files = \Storage::disk('public')->files('products');
        return collect($files)
            ->filter(function ($file) {
                $ext = \Arr::last(explode('.', $file));
                return $ext === 'png';
            });
    }

    private function convertImage(string $file, $key) {
        $image = \Storage::disk('public')->get($file);
        try {
            $image = \Image::make($image)->stream('webp', 100);
            $path = public_path('storage/products/');
            $fileName = \File::name($file);
            $image->save($path . $fileName . '.webp');
            ProductImage::whereProductImage($file)
                ->update(
                    [
                        'product_image' => 'products/' . $fileName . '.webp'
                    ]
                );
        } catch (\Exception $exception) {
            print_r($exception->getMessage());
            $this->error("Exception at " . $key);
        }
    }
}
