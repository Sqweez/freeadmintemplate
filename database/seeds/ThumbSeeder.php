<?php

use Illuminate\Database\Seeder;
use App\v2\Models\Image;

class ThumbSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $images = Image::all();

        DB::table('thumbs')->truncate();
        DB::table('thumbable')->truncate();

        $images->each(function ($image) {
            $imageService = new \App\Http\Controllers\Services\ImageService();
            try {
                $imageOriginalName = \File::name($image['image']);
                $imageOriginalExt = \File::extension($image['image']);
                $width = 300;
                $height = 300;
                $thumbName = "{$imageOriginalName}_{$width}x{$height}.{$imageOriginalExt}";
                if (!Storage::exists("public/" . $thumbName)) {
                    $thumbPath = $imageService->processImage($image['image'], 300, 300);
                } else {
                    $thumbPath = "products\\thumbs\\" . $thumbName;
                }

                $thumb_id = \App\v2\Models\Thumb::create(
                    ['image' => $thumbPath]
                )->id;

                $thumbs = DB::table('imagable')
                    ->where('image_id', $image['id'])
                    ->get()
                    ->map(function ($item) use ($thumbPath, $thumb_id){
                        return [
                            'thumb_id' => $thumb_id,
                            'thumbable_id' => $item->imagable_id,
                            'thumbable_type' => 'App\v2\Models\Product'
                        ];
                    })->unique(function ($item) {
                        return $item['thumbable_id'].$item['thumb_id'];
                    })->values()->all();

                DB::table('thumbable')->insert($thumbs);
            } catch (Exception $exception) {
                //
            }

        });
    }
}
