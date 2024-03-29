<?php

namespace App\Models\traits;

use App\Category;
use App\Manufacturer;
use App\Subcategory;

trait HasOptCatalogLink
{
    public function getOptLink(): string
    {
        $model = get_class($this);
        switch ($model) {
            case Category::class:
                return sprintf('/catalog/category/%s', $this->category_slug);
            case Subcategory::class;
                return sprintf('/catalog/subcategory/%s', $this->subcategory_slug);
            case Manufacturer::class:
                return sprintf('/catalog/brand/%s', $this->id);
            default:
                return '';
        }
    }
}
