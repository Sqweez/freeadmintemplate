<?php

namespace App\Http\Controllers\api;

use App\Arrival;
use App\ArrivalProducts;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArrivalResource;
use App\ProductBatch;
use Illuminate\Http\Request;

class ArrivalController extends Controller
{
    public function index(Request $request) {
        $is_completed = $request->has('is_completed') ? $request->get('is_completed') : 0;
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
                ->orderByDesc('created_at')
                ->get()
                ->map(function ($arrival) {
                    $products = collect($arrival->products)->filter(function ($batch) {
                        return $batch['product'] !== null;
                    });
                    unset($arrival['products']);
                    $arrival['products'] = $products;
                    return $arrival;
                }));
    }

    public function getArrival(Arrival $arrival) {
        return new ArrivalResource($arrival);
    }

    public function createArrival(Request $request) {
        $arrival = Arrival::create($request->except('products'));
        foreach ($request->get('products') as $item) {
            $arrival->products()->create(
                [
                    'product_id' => $item['id'],
                    'count' => $item['count'],
                    'purchase_price' => $item['purchase_price']
                ]
            );
        }

        return new ArrivalResource($arrival);
    }

    public function changeArrival($id, Request $request) {
        ArrivalProducts::where('arrival_id', $id)->delete();
        $products = collect($request->all());
        $products->each(function ($product) use ($id) {
            ArrivalProducts::create([
                'arrival_id' => $id,
                'product_id' => $product['product_id'],
                'count' => $product['count'],
                'purchase_price' => $product['purchase_price']
            ]);
        });
    }

    public function createBatch(Request $request) {
        $arrival = Arrival::find($request->get('arrival_id'));
        $products = $request->get('products');
        foreach ($products as $product) {
            ProductBatch::create([
                'product_id' => $product['product_id'],
                'quantity' => $product['count'],
                'store_id' => $arrival->store_id,
                'purchase_price' => $product['purchase_price'],
                'arrival_id' => $arrival->id,
            ]);
        }

        ArrivalProducts::destroy($arrival->products->pluck('id'));

        foreach ($products as $product) {
            ArrivalProducts::create([
                'product_id' => $product['product_id'],
                'arrival_id' => $arrival->id,
                'count' => $product['count'],
                'purchase_price' => $product['purchase_price']
            ]);
        }

        $arrival->is_completed = true;
        $arrival->save();
    }

    public function cancelArrival(Arrival $arrival) {
        $id = $arrival->id;
        if ($id !== -1) {
            ProductBatch::where('arrival_id', $id)->delete();
            $this->deleteArrival($arrival);
        }
    }

    public function deleteArrival(Arrival $arrival) {
        ArrivalProducts::destroy($arrival->products->pluck('id'));
        $arrival->delete();
    }

    public function update(Arrival $arrival, Request $request) {
        $arrival->update($request->all());
        return new ArrivalResource($arrival);
    }
}
