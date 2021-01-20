<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\v2\Models\Image;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function category() {
        $files =  \Storage::disk('public')->files('category');
        foreach ($files as $file) {
            $image = \Storage::get('public/' . $file);
            $image = \Image::make($image)->encode('webp');
            $path = public_path('storage/category/');
            $fileName = \File::name($file);
            $image->save($path . $fileName . '.webp');
        }
    }

/*    public function products() {
        $files = \Storage::disk('public')->files('products');
        foreach ($files as $key => $file) {
            $image = \Storage::get('public/' . $file);
            try {
                $image = \Image::make(imagecreatefrompng($image))->encode('webp');
                $path = public_path('storage/products/');
                $fileName = \File::name($file);
                $image->save($path . $fileName . '.webp');
            } catch (\Exception $exception) {
                echo "exception at " . $key;
            }

        }
    }*/

    public function productsResize() {

    }
}
