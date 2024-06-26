<?php

namespace App\Http\Controllers\api\opt\admin\v1;

use App\Http\Controllers\api\BaseApiController;
use App\Http\Resources\Opt\Admin\OrdersResource;
use App\Jobs\Opt\CreateInvoiceDocument;
use App\Jobs\Opt\CreateWaybillDocument;
use App\Repository\Opt\OrderRepository;
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
                    ->with('products:id,product_id,price,wholesale_order_id,discount')
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
        $order->load('products:id,product_id,price,wholesale_order_id,discount');
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
                   ->with(['product.batches' => function ($query) {
                       return $query->whereStoreId(32);
                   }])
                   ->get()
                   ->groupBy('product_id')
                   ->map(function ($items) {
                       return collect($items)
                           ->groupBy('discount')
                           ->map(function ($items) {
                               /* @var WholesaleOrderProduct $product */
                               $product = $items->first();
                               return [
                                   'id' => $product->id,
                                   'product_image' => $product->product->product->retrieveProductThumb(),
                                   'product_name' => $product->product->product->product_name,
                                   'manufacturer' => $product->product->product->manufacturer->manufacturer_name,
                                   'product_sub_name' => $product->product->product->attributes->pluck('attribute_value')->join(' '),
                                   'link' => $product->product->product->getOptLink(),
                                   'count' => $items->count(),
                                   'type' => $product->product->attributes->pluck('attribute_value')->join(' '),
                                   'stock_count' => $product->product->batches->sum('quantity') + $items->count(),
                                   'initial_count' => $items->count(),
                                   'product_id' => $product->product_id,
                                   'price_per_item' => $product->price,
                                   'discounted_price_per_item' => $product->final_price,
                                   'discount' => $product->discount,
                                   'ids' => $items->pluck('id')
                               ];
                           })
                           ->values();
                   })
                   ->flatten(1)
        ]);
    }

    public function uploadInvoice(Request $request, WholesaleOrder $order): JsonResponse
    {
        $file = $request->file('invoice');
        $fileName = $order->getInvoiceFileName() . '.' . $file->getClientOriginalExtension();;
        $path = $file->storeAs('opt/invoices', $fileName, 'public');
        $order->update(['invoice' => $path]);
        return $this->respondSuccess([
            'invoice' => url('/') . \Storage::url($path),
        ]);
    }

    public function uploadWaybill(Request $request, WholesaleOrder $order): JsonResponse
    {
        $file = $request->file('waybill');
        $fileName = $order->getWaybillFileName() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('opt/waybills', $fileName, 'public');
        $order->update(['waybill' => $path]);
        return $this->respondSuccess([
            'waybill' => url('/') . \Storage::url($path),
        ]);
    }

    /**
     * @throws \Exception
     */
    public function update(Request $request, WholesaleOrder $order): JsonResponse
    {
        $status_id = $request->get('status_id');
        $order->changeStatus($status_id, auth()->id());
        return $this->show($order);
    }

    /**
     * @throws \Exception
     */
    public function updateOrderProducts(Request $request, WholesaleOrder $order, OrderRepository $orderRepository): JsonResponse
    {
        $products = $request->get('products', []);
        $deletedProducts = $request->get('deleted', []);
        try {
            $orderRepository->updateOrder($order, $products, $deletedProducts);
            if ($request->get('refreshDocs', false) === true) {
                CreateWaybillDocument::dispatchNow($order);
                CreateInvoiceDocument::dispatchNow($order);
            }
            return $this->show($order);
        } catch (\Throwable $exception) {
            return $this->respondError(['message' => $exception->getMessage()]);
        }
    }
}
