<?php

namespace App\Http\Controllers\api\v2;

use App\Category;
use App\Http\Controllers\Controller;
use App\Subcategory;
use Illuminate\Http\Request;

class SeoController extends Controller
{
    public function storeText($type, $id, Request $request) {
        $content = $request->get('content');
        $model = null;
        switch ($type) {
            case 'category':
                $model = Category::query();
                break;
            case 'subcategory':
                $model = Subcategory::query();
                break;
            default:
                break;
        }

        if (!$model) {
            return response(['message' => 'Произошла ошибка'], 500);
        }

        $model = $model->whereKey($id)->first();

        $model->update([
            'meta_h1' => $request->get('meta_h1', null),
            'meta_title' => $request->get('meta_title', null),
            'meta_description' => $request->get('meta_description', null),
        ]);

        $model->seoText()->delete();

        $model->seoText()->create([
            'content' => $content
        ]);

        return response([]);
    }
}
