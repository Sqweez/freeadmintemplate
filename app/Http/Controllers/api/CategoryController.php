<?php

namespace App\Http\Controllers\api;

use App\Category;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\shop\ShopCategoryResource;
use App\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CategoryController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Exception
     */
    public function index() {
        if (Cache::has('categories')) {
            return Cache::get('categories');
        } else
        return cache()->remember('categories', 24 * 60 * 60 * 30 * 365, function () {
            return CategoryResource::collection(Category::all());
        });
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return CategoryResource
     */
    public function store(Request $request) {
        $_category = $request->except('subcategories');
        $_category['category_slug'] = Str::slug($_category['category_name']);
        $category = Category::create($_category);
        $category_id = $category['id'];
        $subcategories = $request->input('subcategories');
        foreach ($subcategories as $subcategory) {
            Subcategory::create([
                'subcategory_name' => $subcategory,
                'category_id' => $category_id,
                'subcategory_slug' => Str::slug($subcategory)
            ]);
        }

        return new CategoryResource(Category::find($category_id));
    }

    public function indexShop() {
        return cache()->remember('shop-categories', 24 * 60 * 60 * 30 * 365, function () {
            return ShopCategoryResource::collection(Category::all());
        });
        //return ShopCategoryResource::collection(Category::all());
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Category $category
     * @return CategoryResource
     */
    public function update(Request $request, Category $category) {
        $subcategories = $request->get('subcategories');
        $category_name = $request->get('name');
        $category->update(['category_name' => $category_name, 'category_img' => $request->get('category_img')]);
        foreach ($subcategories as $subcategory) {
            if (gettype($subcategory) === 'string' && strlen($subcategory) > 0) {
                Subcategory::create(['subcategory_name' => $subcategory, 'category_id' => $category['id']]);
            } else {
                $_subcategory = Subcategory::find($subcategory['id']);
                if (strlen($subcategory['subcategory_name']) === 0) {
                    $_subcategory->delete();
                } else {
                    $_subcategory->update(['subcategory_name' => $subcategory['subcategory_name']]);
                }
            }
        }

        return new CategoryResource($category);
    }

    public function destroy(Category $category) {
        $category->delete();
    }

    public function slugs() {
        $categories = Category::all();
        $subcategories = Subcategory::all();
        foreach ($categories as $category) {
            $category_slug = Str::slug($category['category_name'] . ' ' . $category['id'], '-');
            $category->update(['category_slug' => $category_slug]);
        }

        foreach ($subcategories as $category) {
            $category_slug = Str::slug($category['subcategory_name'] . ' ' . $category['id'], '-');
            $category->update(['subcategory_slug' => $category_slug]);
        }
        return $categories;
    }

}
