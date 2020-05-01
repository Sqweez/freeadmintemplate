<?php


namespace App\Http\Controllers\Services;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageService {
    public static function upload(Request $request) {
        $file = $request->file('file');
        $path = $request->get('path');
        $path = $file->store($path, 'public');
        return $path;
    }

    public static function delete(Request $request) {
        $file = $request->get('file');
        Storage::disk('public')->delete($file);
        return;
    }
}
