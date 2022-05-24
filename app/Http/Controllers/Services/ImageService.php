<?php


namespace App\Http\Controllers\Services;


use Illuminate\Http\Request;
use Storage;
use Intervention\Image\Facades\Image;

class ImageService {

    public function generateThumb(Request $request) {
        $validator = \Validator::make($request->all(), [
            'image' => 'required'
        ], [
            'image.required' => 'Не передан параметр изображение!'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 422);
        }

        $image = $request->get('image');
        $width = $request->get('width', 300);
        $height = $request->get('height', 300);
        try {
            $isArray = gettype($image) === 'array';
            if ($isArray) {
                return collect($image)->map(function ($i) use ($width, $height) {
                    return $this->processImage($i, $width, $height);
                });
            }

            return $this->processImage($image, $width, $height);

        } catch (\Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function processImage(string  $image, $width, $height): string {
        $originalImage = $this->getImage($image);
        $resizedImage = $this->resizeImage($originalImage, $width, $height);
        $encodedImage = $this->encodeImage($resizedImage, $image);
        $imageName = $this->getImageName($image, $width, $height);
        $imageDir = $this->getImageDir($image);
        return $this->saveImage($encodedImage, "{$imageDir}{$imageName}");
    }

    private function getImage(string $image) {
        return Storage::get("public/{$image}");
    }

    private function resizeImage($image, $width, $height): \Intervention\Image\Image {
        return Image::make($image)->fit($width, $height, function ($constraint) {
            $constraint->upsize();
        });
    }

    private function encodeImage($image, $imagePath, $quality = 100): \Intervention\Image\Image {
        return $image->encode(\File::extension($imagePath), $quality);
    }

    private function getImageName($imagePath, $width, $height) {
        $imageOriginalName = \File::name($imagePath);
        $imageOriginalExt = \File::extension($imagePath);
        return "{$imageOriginalName}_{$width}x{$height}.{$imageOriginalExt}";
    }

    private function getImageDir($imagePath) {
        $imageOriginalDir = \File::dirname('public/' . $imagePath);
        $dir = $imageOriginalDir . "\\thumbs\\";
        if (!Storage::exists($dir)) {
            Storage::makeDirectory($dir);
        }
        return $dir;
    }

    private function saveImage(\Intervention\Image\Image $image, $path): string {
        Storage::put($path, $image);
        return \Str::replaceFirst('public/', '', $path);
    }

}
