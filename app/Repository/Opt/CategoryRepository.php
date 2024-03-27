<?php

namespace App\Repository\Opt;

use App\Category;
use App\Http\Resources\Opt\CategoryResource;

class CategoryRepository extends BaseRepository
{
    public function get()
    {
        $categories =  $this->getFromCache(function () {
            return Category::query()
                ->tap(function ($query) {
                    return $this->havingOptProducts($query);
                })
                ->with([
                    'subcategories' => function ($query) {
                        return $this->havingOptProducts($query);
                    }
                ])
                ->get();
        });

        return CategoryResource::collection($categories);
    }
}
