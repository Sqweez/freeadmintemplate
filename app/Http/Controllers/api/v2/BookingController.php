<?php

namespace App\Http\Controllers\api\v2;

use App\Arrival;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\SaleService;
use App\Http\Resources\Booking\BookingResource;
use App\ProductBatch;
use App\Sale;
use App\v2\Models\Booking;
use App\v2\Models\BookingProduct;
use Illuminate\Http\Request;
use SebastianBergmann\Comparator\Book;

class BookingController extends Controller
{
    public function index(Request $request) {
        return BookingResource::collection(
            Booking::query()
                ->when($request->has('start'), function ($query) use ($request) {
                    return $query->whereDate('created_at', '<=', $request->get('start'));
                })
                ->when($request->has('finish'), function ($query) use ($request) {
                    return $query->whereDate('created_at', '>=', $request->get('finish'));
                })
                ->with([
                'user:id,name',
                'store:id,name',
                'client:id,client_name',
                'products',
                'products.product:id,product_id',
                'products.product.product:id,product_name,product_price,manufacturer_id',
                'products.product.attributes',
                'products.product.product.attributes',
                'products.product.product.manufacturer',
                'arrival'
            ])->get()
        );
    }

    public function show($id) {
        return new BookingResource(Booking::findOrFail($id));
    }

    public function store(Request $request) {
        $booking = $request->get('booking');
        $products = $request->get('products');
        $_booking = Booking::create($booking);
        collect($products)->filter(function ($product) {
            return $product['count'] > 0;
        })->each(function ($product) use ($_booking) {
            $_booking->products()->create([
                'arrival_id' => $_booking['id'],
                'product_id' => $product['product_id'],
                'count' => $product['count'],
                'product_price' => $product['product_price'],
                'arrival_product_id' => $product['arrival_product_id'],
                'purchase_price' => $product['purchase_price']
            ]);
        });

        $_booking->load('products');
        return $booking;
    }

    public function destroy($id) {
        $booking = Booking::find($id);
        $arrival = Arrival::find($booking->arrival_id);
        $booking->update(['is_sold' => -1]);
        if ($arrival->is_completed) {
            $bookingProducts = BookingProduct::whereBookingId($id)->get();
            $bookingProducts->each(function ($product) use ($booking){
                ProductBatch::create([
                    'product_id' => $product['product_id'],
                    'quantity' => $product['count'],
                    'store_id' => $booking->store_id,
                    'purchase_price' => $product['purchase_price'],
                    'arrival_id' => $booking->arrival_id,
                ]);
            });
        }
        BookingProduct::whereBookingId($id)->delete();
        $booking->delete();
    }

    public function createSale(Request $request, SaleService $saleService) {
        \DB::beginTransaction();
        $booking = Booking::find($request->get('booking_id'));
        $products = $booking->products;
        $products->each(function ($product) use ($booking) {
            ProductBatch::create([
                'product_id' => $product['product_id'],
                'quantity' => $product['count'],
                'store_id' => $booking['store_id'],
                'purchase_price' => $product['purchase_price']
            ]);
        });

        // Create Sale
        $sale = Sale::create([
            'client_id' => $booking->client_id,
            'store_id' => $booking->store_id,
            'user_id' => $request->get('user_id', 1),
            'discount' => $request->get('discount', 0),
            'comment' => $request->get('comment'),
            'balance' => 0,
            'payment_type' => $request->get('payment_type'),
            'split_payment' => $request->get('split_payment'),
            'is_delivery' => $request->get('is_delivery'),
            'booking_id' => $booking->id,
        ]);

        $products = $products->map(function ($product) {
            $product['discount'] = 0;
            unset($product['id']);
            $product['id'] = $product['product_id'];
            return $product;
        });

        $saleService->createSaleProducts($sale, $booking->store_id, $products->toArray());
        $saleService->createClientSale($booking->client_id, $sale->discount, $products->toArray(), 0, $request->get('user_id'), $sale->id, null);
        $booking->is_sold = 1;
        $booking->save();
        \DB::commit();
    }
}
