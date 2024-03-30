<?php

namespace App\Resolvers\Meta;

use App\Category;
use App\Manufacturer;
use App\Subcategory;
use App\v2\Models\Product;

class OptMetaCatalogResolver
{

    public function resolver(array $params)
    {
        return \Cache::remember($this->getCacheKey($params), 60 * 60, function () use ($params) {
            return [
                'heading' => $this->getHeading($params),
                'description' => 'Iron-Addicts.KZ | оптовые товары',
                'keywords' => ['каталог'],
                'title' => $this->getTitle($params),
            ];
        });

    }

    private function getCacheKey(array $params)
    {
        return json_encode($params);
    }

    private function getTitle(array $params): string
    {
        if ($entity = $this->getEntity($params)) {
            return sprintf('Купить %s оптом по лучшим ценам', $entity->getNameAttribute());
        }

        return 'Iron-Addicts.KZ | оптовые товары';
    }

    private function getHeading(array $params): string
    {
        if ($entity = $this->getEntity($params)) {
            return $entity->getNameAttribute();
        }

        return 'Каталог';
    }

    private function getEntity(array $params)
    {
        if (isset($params[Product::FILTER_CATEGORIES])) {
            return Category::find($params[Product::FILTER_CATEGORIES]);
        }

        if (isset($params[Product::FILTER_SUBCATEGORIES])) {
            return Subcategory::find($params[Product::FILTER_SUBCATEGORIES]);
        }

        if (isset($params[Product::FILTER_BRANDS])) {
            return Manufacturer::find($params[Product::FILTER_BRANDS]);
        }

        return null;
    }
}
