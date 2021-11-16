<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use App\Http\Resources\shop\v2\NewsResource;
use App\NewsProduct;
use App\v2\Models\Image;
use App\v2\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request) {
        if ($request->has('shop')) {
            $news = News::with(
                [
                    'news_image', 'productNews'
                ]
            )->get()->sortByDesc('created_at');
            return NewsResource::collection($news);
        }
        return News::with(['news_image', 'productNews'])->get();
    }

    public function store(Request $request) {
        $req = $request->only(['title', 'text', 'short_text']);
        $news = News::create($req);
        $image = Image::create(['image' => $request->get('image')])->id;
        $news->news_image()->sync([$image]);
        $products = $request->get('products');
        foreach ($products as $product) {
            NewsProduct::create([
                'product_id' => $product,
                'news_id' => $news->id
            ]);
        }
        return News::with('news_image')
            ->with('productNews')
            ->whereKey($news->id)
            ->get()
            ->first();

    }

    public function show($id) {

    }

    public function destroy($id) {
        $news = News::find($id);
        $news->news_image()->delete();
        NewsProduct::where('news_id', $id)->delete();
        $news->delete();
    }

    public function update(News $news, Request $request) {
        $req = $request->only(['title', 'text', 'short_text']);
        $news->update($req);
        $image = Image::whereImage($request->get('image'))->firstOrCreate(['image' => $request->get('image')])->id;
        $news->news_image()->sync([$image]);
        NewsProduct::where('news_id', $news->id)->delete();
        $products = $request->get('products');
        foreach ($products as $product) {
            NewsProduct::create([
                'product_id' => $product,
                'news_id' => $news->id
            ]);
        }
        return News::with('news_image')->whereKey($news->id)->get()->first();
    }
}
