<?php

namespace App\Http\Controllers;

use App\Client;
use App\Http\Controllers\api\WaybillController;
use App\Http\Resources\v2\Product\ProductsResource;
use App\ProductBatch;
use App\Promocode;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use ProductService;

class CronController extends Controller
{
    public function disablePartners(Request $request) {
        $clients = Client::Partner()->whereDate('partner_expired_at', now())->pluck('id');
        Promocode::OfPartner($clients)->update(
            [
                'is_active' => false
            ]
        );

        Client::where('id', $clients)->update(
            [
                'is_partner' => false
            ]
        );
    }

    public function revokeSellerToken() {
        $users = User::query()
            ->where('role_id', 2)
            ->orWhere('id', 29)
            ->get();
        return $users->each(function (User $user) {
            return $user->update(['token' => Str::random(60)]);
        });
    }

    public function storePriceList(Request $request) {
        $products = ProductsResource::collection(ProductService::all())->toArray($request);
        $batches = ProductBatch::query()
            ->whereIn('store_id', [1, 6])
            ->where('quantity', '>', 0)
            ->select(['quantity', 'product_id'])
            ->get();

        $quantities = $batches
            ->groupBy('product_id')
            ->map(function ($item, $key) {
                return [
                    'product_id' => $key,
                    'quantity' => collect($item)->reduce(function ($a, $c) {
                        return $a + $c['quantity'];
                    }, 0)
                ];
            })->values();

        $products = collect($products)->map(function ($product) use ($quantities) {
            $id = $product['id'];
            $qnt = collect($quantities)->filter(function ($q) use ($id) {
                return $q['product_id'] == $id;
            })->reduce(function ($a, $c) {
                return $a + $c['quantity'];
            }, 0);
            unset($product['quantity']);
            $product['quantity'] = $qnt;
            return $product;
        })->values()->filter(function ($i) {
            return $i['quantity'] > 0;
        })->values()->sortBy('manufacturer_id')->values();


        return (new WaybillController())->getPriceList(new Request(), $products);
    }
}
