<?php

namespace App\Http\Controllers\api\v2;

use App\Arrival;
use App\Http\Controllers\api\BaseApiController;
use App\Http\Resources\ArrivalResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ArrivalController extends BaseApiController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $is_completed = !!$request->get('is_completed', 0);
        return ArrivalResource::collection(
            Arrival::where('is_completed', $is_completed)
                ->with([
                    'products', 'products.product',
                    'products.product.product',
                    'products.product.product.manufacturer',
                    'products.product.product_images',
                    'products.product.attributes',
                    'products.product.attributes.attribute_name',
                    'products.product.product.attributes',
                    'products.product.product.attributes.attribute_name',
                    'user', 'store', 'bookings', 'bookings.products',
                    'products.bookingProducts'
                ])
                ->when($request->has('search'), function ($query) use ($request) {
                    return $query->whereHas('products', function ($query) use ($request) {
                        return $query->whereHas('product', function ($query) use ($request) {
                            return $query->whereHas('product', function ($query) use ($request) {
                                return $query->where('product_name', 'like', '%' . $request->get('search') . '%');
                            });
                        });
                    });
                })
                ->orderByDesc('created_at')
                ->paginate(10)
        );
    }
}
