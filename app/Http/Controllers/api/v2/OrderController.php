<?php

namespace App\Http\Controllers\api\v2;

use App\Client;
use App\ClientSale;
use App\ClientTransaction;
use App\Http\Controllers\api\CartController;
use App\Http\Controllers\Controller;
use App\Http\Resources\v2\Order\OrderResource;
use App\Order;
use App\OrderProduct;
use App\ProductBatch;
use App\Sale;
use App\SaleProduct;
use App\User;
use App\v2\Models\Image;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Response;
use TelegramService;

class OrderController extends Controller
{

    public function getOrders(): AnonymousResourceCollection {
        $orders = Order::query()
            ->with(
                [
                    'store:id,name', 'items',
                    'items.batch.store',
                    'items.product', 'items.product.attributes',
                    'items.product.product', 'items.product.product.attributes',
                    'items.product.product.manufacturer', 'image'
                ]
            )
            ->orderByDesc('created_at')
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->get();
        return OrderResource::collection($orders);
    }

    public function restoreOrder(Order $order) {
        $sale = Sale::whereOrderId($order->id)->first();
        if (!$sale) {
            return response()->json([
                'message' => 'Невозможно восстановить заказ, потому что он старый!'
            ], 500);
        }
        SaleProduct::whereSaleId($sale->id)->delete();
        if (intval($sale->client_id) !== -1) {
            ClientSale::where('sale_id', $sale->id)->first()->delete();
            ClientTransaction::where('sale_id', $sale->id)->first()->delete();
        }
        $sale->delete();
        $order->update(['status' => 0]);
        return response()->json([], 200);
    }

    public function getOrder($id) {
        return new OrderResource(Order::with(
            [
                'store:id,name', 'items',
                'items.batch.store',
                'items.product', 'items.product.attributes',
                'items.product.product', 'items.product.product.attributes',
                'items.product.product.manufacturer', 'image'
            ]
        )->whereKey($id)->first());
    }

    public function changeProducts(Order $order, Request $request) {
        $products = $request->get('products');
        $old_products = collect($products)->filter(function ($product) {
            return isset($product['order_item_id']);
        });
        $new_products = collect($products)->filter(function ($product) {
            return !isset($product['order_item_id']);
        });
        $order_products = OrderProduct::where('order_id', $order->id)->get()->each(function ($product) use ($old_products) {
            $exists = $old_products->filter(function ($a) use ($product) {
                    return $a['id'] === $product['id'];
                })->count() > 0;
            if (!$exists) {
                $batch = ProductBatch::find($product['product_batch_id']);
                $batch->quantity = $batch->quantity + 1;
                $batch->save();
                OrderProduct::whereKey($product['id'])->delete();
            }
        });

        $new_products->each(function ($product) use ($order) {
            $batch = ProductBatch::where('quantity', '>', 0)
                ->where('store_id', $product['store']['id'])
                ->where('product_id', $product['id'])->first();
            if ($batch) {
                $batch->quantity = $batch->quantity - 1;
                $batch->save();
                OrderProduct::create([
                    'order_id' => $order->id,
                    'product_batch_id' => $batch->id,
                    'product_id' => $product['id'],
                    'purchase_price' => $batch->purchase_price,
                    'product_price' => $product['product_price']
                ]);
            }
        });

        return new OrderResource(Order::with(
            [
                'store:id,name', 'items',
                'items.batch.store',
                'items.product', 'items.product.attributes',
                'items.product.product', 'items.product.product.attributes',
                'items.product.product.manufacturer', 'image'
            ]
        )->whereKey($order->id)->first());
    }

    public function changeClient(Order $order, Request $request) {
        $client_id = $request->get('client_id');
        $client = Client::find($client_id);
        $discountPercent = intval($client->calculateDiscountPercent());
        $order->update([
            'client_id' => $client_id,
            'discount'  => $discountPercent,
            'fullname' => $client->client_name
        ]);
        return new OrderResource(Order::with(
            [
                'store:id,name', 'items',
                'items.batch.store',
                'items.product', 'items.product.attributes',
                'items.product.product', 'items.product.product.attributes',
                'items.product.product.manufacturer', 'image'
            ]
        )->whereKey($order->id)->first());
    }

    public function update(Order $order, Request $request): OrderResource {
        $order->update($request->all());
        return new OrderResource(Order::with(
            [
                'store:id,name', 'items',
                'items.batch.store',
                'items.product', 'items.product.attributes',
                'items.product.product', 'items.product.product.attributes',
                'items.product.product.manufacturer', 'image'
            ]
        )->whereKey($order->id)->first());
    }

    public function deleteOrder(Order $order) {
        $order->delete();
    }

    public function accept(Order $order) {
        if ($order['status'] == 1) {
            return ['error' => 'Заказ уже выполнен!'];
        }

        if ($order['status'] == -1) {
            return ['error' => 'Заказ уже отменен!'];
        }


        $store_id = $order['store_id'];
        $products = $order->items;

        $sale = Sale::create([
            'client_id' => $order['client_id'],
            'store_id' => $store_id,
            'user_id' => User::IRON_WEB_STORE,
            'discount' => $order['discount'],
            'kaspi_red' => 0,
            'balance' => $order['balance'] ?? 0,
            'order_id' => $order->id,
            'promocode_id' => $order->promocode_id,
            'promocode_fixed_amount' => $order->promocode_fixed_amount,
            // Безналичная оплата для интернет-заказов
            'payment_type' => 1,
        ]);


        $products->each(function ($product) use ($sale) {
            SaleProduct::create([
                'product_batch_id' => $product['product_batch_id'],
                'product_id' => $product['product_id'],
                'sale_id' => $sale['id'],
                'purchase_price' => $product['purchase_price'],
                'product_price' => $product['product_price'],
                'discount' => max($product->discount, $sale->discount)
            ]);
        });

        (new CartController())->createClientSale($sale);

        $order->status = 1;

        $order->update();

        $message = 'Заказ №' . $order->id . ' выполнен 💪💪💪';

        TelegramService::sendMessage(env('TELEGRAM_KZ_CHAT_ID'), urlencode($message));

        return 'Заказ выполнен!';
    }

    public function decline(Order $order) {
        if ($order['status'] == 1) {
            return ['error' => 'Заказ уже выполнен!'];
        }

        if ($order['status'] == -1) {
            return ['error' => 'Заказ уже отменен!'];
        }

        $order->status = -1;

        $order->update();

        $products = $order->items;

        foreach ($products as $product) {
            $productBatch = ProductBatch::find($product['product_batch_id']);
            $productBatch->quantity = $productBatch->quantity + 1;
            $productBatch->update();
        }

        $message = 'Заказ №' . $order->id . ' отменен 😠😠😠';

        TelegramService::sendMessage(env('TELEGRAM_KZ_CHAT_ID'), urlencode($message));

        return 'Заказ отменен!';

    }

    public function setImage(Order $order, Request $request) {
        $image = $request->get('file');
        $image_id = Image::create(['image' => $image])->id;
        $order->image()->sync([$image_id]);

        return new OrderResource(Order::find($order->id));
    }

    /**
     * @throws GuzzleException
     */
    public function getPaymentInvoice(Order $order, Request $request) {
        $order = Order::query()
            ->with(
                [
                    'store:id,name', 'items',
                    'items.batch.store',
                    'items.product', 'items.product.attributes',
                    'items.product.product', 'items.product.product.attributes',
                    'items.product.product.manufacturer', 'image'
                ]
            )
            ->orderByDesc('created_at')
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->whereKey($order->id)
            ->first();

        $orderResource = (new OrderResource($order))->toArray($request);
        $client = new \GuzzleHttp\Client();
        $products = $orderResource['products'];
        $products = array_map(function ($product) {
            $product['attributes'] = $product['attributes']->toArray();
            $product['manufacturer'] = $product['manufacturer']->toArray();
            $product['product_price'] = $product['product_price_rub'];
            return $product;
        }, $products);
        $response = $client->post(url('/') . '/api/v2/documents/invoice-payment', [
            'form_params' => [
                'cart' => $products,
                'customer' => $orderResource['client_name']
            ]
        ])->getBody()->getContents();
        $path = json_decode($response, true)['path'];
        return Response::download($path);
    }
}
