<?php

namespace App\Http\Controllers\api;

use App\Category;
use App\Http\Controllers\Controller;
use App\Subcategory;
use Illuminate\Http\Request;

class SlugController extends Controller
{
    public function index(Request $request) {
        $slug = $request->get('slug');
        return $this->getBySlug($slug);
    }

    private function getBySlug($slug) {
        $category = Category::ofSlug($slug)->first();
        if ($category) {
            return [
                'category' => $category['id'],
            ];
        }

        else {
            $subcategory = Subcategory::ofSlug($slug)->first();

            if (!$subcategory) {
                return 500;
            }
            $category = Category::find($subcategory['category_id']);

            return [
                'category' => $category['id'],
                'subcategory' => $subcategory['id']
            ];
        }
    }
}
