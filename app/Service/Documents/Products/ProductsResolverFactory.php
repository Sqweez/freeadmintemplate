<?php

namespace App\Service\Documents\Products;

use App\v2\Models\WholesaleOrder;
use Exception;
use Illuminate\Database\Eloquent\Model;

class ProductsResolverFactory
{
    /**
     * @throws Exception
     */
    public static function create(Model $entity): ProductsResolverInterface
    {
        if ($entity instanceof WholesaleOrder) {
            return new WholesaleOrderProductsResolver($entity);
        }
        throw new Exception('Entity не является допустимой сущностью');
    }
}
