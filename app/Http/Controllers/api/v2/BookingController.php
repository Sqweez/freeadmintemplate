<?php

namespace App\Http\Controllers\api\v2;

use App\Arrival;
use App\Http\Controllers\Controller;
use App\Http\Resources\Booking\BookingResource;
use App\v2\Models\Booking;
use App\v2\Models\BookingProduct;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request) {
        return BookingResource::collection(
            Booking::with([
                'user:id,name',
                'store:id,name',
                'client:id,client_name',
                'products',
                'products.product:id,product_id',
                'products.product.product:id,product_name,product_price,manufacturer_id',
                'products.product.attributes',
                'products.product.product.attributes',
                'products.product.product.manufacturer',
            ])->get()
        );
    }

    public function store(Request $request) {
        $booking = $request->get('booking');
        $products = $request->get('products');
        $_booking = Booking::create($booking);
        collect($products)->each(function ($product) use ($_booking) {
            $_booking->products()->create([
                'arrival_id' => $_booking['id'],
                'product_id' => $product['product_id'],
                'count' => $product['count'],
                'product_price' => $product['product_price'],
                'arrival_product_id' => $product['arrival_product_id'],
            ]);
        });

        $_booking->load('products');
        return $booking;
    }

    public function destroy($id) {
        $booking = Booking::find($id);
        $arrival = Arrival::find($booking->arrival_id);
        BookingProduct::whereBookingId($id)->delete();
        //Booking::whereId($id)->delete();
    }
}
