<?php


namespace App\Http\Controllers\Services;


use Illuminate\Http\Request;
use Storage;

class FileService {
    public static function upload(Request $request) {
        $file = $request->file('file');
        $path = $request->get('path');
        $path = $file->store($path, 'public');
        return response( $path, 200);
    }

    public static function uploadData($file, $path) {
        $path = $file->store($path, 'public');
        return $path;
    }

    public static function delete(Request $request) {
        $file = $request->get('file');
        Storage::disk('public')->delete($file);
        return response()->noContent();
    }
}
