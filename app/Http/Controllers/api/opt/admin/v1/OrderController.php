<?php

namespace App\Http\Controllers\api\opt\admin\v1;

use App\Http\Controllers\api\BaseApiController;
use App\Http\Resources\Opt\Admin\OrdersResource;
use App\v2\Models\WholesaleOrder;
use App\v2\Models\WholesaleOrderProduct;
use App\v2\Models\WholesaleOrderStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends BaseApiController
{
    public function get(): JsonResponse
    {
        return $this->respondSuccess([
            'orders' => OrdersResource::collection(
                WholesaleOrder::query()
                    ->with('products:id,product_id,price,wholesale_order_id')
                    ->with('status.status')
                    ->with('client.city')
                    ->orderByDesc('created_at')
                    ->get()
            ),
        ]);
    }

    public function show(WholesaleOrder $order): JsonResponse
    {
        $order->load('client.city');
        $order->load('status.status');
        $order->load('products:id,product_id,price,wholesale_order_id');
        return $this->respondSuccess([
            'order' => new OrdersResource($order),
            'statuses' => WholesaleOrderStatus::all(),
            'products' =>
               $order->products()->with([
                   'product.product',
                   'product.product.attributes',
                   'product.attributes',
                   'product.product.product_thumbs',
                   'product.product.manufacturer'
               ])
                   ->get()
                   ->groupBy('product.product_id')
                   ->map(function ($items) {
                       /* @var WholesaleOrderProduct $product */
                       $product = $items->first();
                       return [
                           'id' => $product->id,
                           'product_image' => $product->product->product->retrieveProductThumb(),
                           'product_name' => $product->product->product->product_name,
                           'manufacturer' => $product->product->product->manufacturer->manufacturer_name,
                           'product_sub_name' => $product->product->product->attributes->pluck('attribute_value')->join(' '),
                           'total_price' => $items->sum('price'),
                           'link' => $product->product->product->getOptLink(),
                           'items' => $items
                               ->groupBy('product_id')
                               ->map(function ($items, $product_id) {
                                   /* @var WholesaleOrderProduct $product */
                                   $product = $items->first();
                                   return [
                                       'id' => $product->id,
                                       'count' => $items->count(),
                                       'total_price' => $items->sum('price'),
                                       'type' => $product->product->attributes->pluck('attribute_value')->join(' '),
                                   ];
                               })
                               ->values()

                       ];
                   })
                   ->values()
        ]);
    }

    public function uploadInvoice(Request $request, WholesaleOrder $order): JsonResponse
    {
        $file = $request->file('invoice');
        $fileName = $order->getInvoiceFileName() . '.' . $file->getClientOriginalExtension();;
        $path = $file->storeAs('public/opt/invoices', $fileName, 'public');
        $order->update(['invoice' => $path]);
        return $this->respondSuccess([
            'invoice' => url('/') . \Storage::url($path),
        ]);
    }

    public function uploadWaybill(Request $request, WholesaleOrder $order): JsonResponse
    {
        $file = $request->file('waybill');
        $fileName = $order->getWaybillFileName() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/opt/waybills', $fileName, 'public');
        $order->update(['waybill' => $path]);
        return $this->respondSuccess([
            'waybill' => url('/') . \Storage::url($path),
        ]);
    }
}
