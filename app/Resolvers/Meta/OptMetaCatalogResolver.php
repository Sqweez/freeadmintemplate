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
            $entity = $this->getEntity($params);
            return [
                'heading' => $this->getHeading($entity),
                'description' => 'Iron-Addicts.KZ | оптовые товары',
                'keywords' => ['каталог'],
                'title' => $this->getTitle($entity),
            ];
        });

    }

    private function getCacheKey(array $params)
    {
        return json_encode($params);
    }

    private function getTitle($entity = null): string
    {
        if ($entity) {
            return sprintf('Купить %s оптом по лучшим ценам', $entity->getNameAttribute());
        }

        return 'Iron-Addicts.KZ | оптовые товары';
    }

    private function getHeading($entity = null): string
    {
        if ($entity) {
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
