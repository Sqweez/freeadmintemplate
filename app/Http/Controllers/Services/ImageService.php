<?php


namespace App\Http\Controllers\Services;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Image;

class ImageService {
    public static function makeThumb(Request $request) {
        /*$file = $request->file('file');
        $image = Image::make($file);
        return $path;*/
    }
}
