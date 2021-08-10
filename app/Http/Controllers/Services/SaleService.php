<?php


namespace App\Http\Controllers\Services;


use App\Client;
use App\CompanionSaleProduct;
use App\CompanionTransaction;
use App\ProductBatch;
use App\Sale;
use App\Transfer;

class SaleService {

    public function createSale($sale): Sale{
        return Sale::create($sale);
    }

    public function createSaleProducts(Sale $sale, $store_id, array $cart) {
        collect($cart)->each(function ($product) use ($sale, $store_id) {
            for ($i = 0; $i < $product['count']; $i++) {
                $batch = ProductBatch::whereProductId($product['id'])->whereStoreId($store_id)->where('quantity', '>', 0)->first();
                $discount = max($product['discount'], $sale->discount);
                $sale->products()->create([
                    'product_batch_id' => $batch->id,
                    'product_id' => $product['id'],
                    'purchase_price' => $batch->purchase_price,
                    'product_price' => $product['product_price'],
                    'discount' => $discount,
                ]);
                $batch->decrement('quantity');
                $batch->save();
            }
        });
    }


    public function createClientSale($client_id, $discount, $cart, $balance, $user_id, $sale_id, $partner_id) {
        $client = Client::find($client_id);
        if (!$client) {
            return false;
        }

        $amount = $this->getTotalCost($cart, $discount);

        $client->sales()->create([
            'sale_id' => $sale_id,
            'amount' => $amount
        ]);

        $client->transactions()->create([
            'sale_id' => $sale_id,
            'user_id' => $user_id,
            'amount' => $amount * ($client->loyalty->cashback / 100)
        ]);

        if ($balance > 0) {
            $client->transactions()->create([
                'sale_id' => $sale_id,
                'user_id' => $user_id,
                'amount' => $balance * -1
            ]);
        }

        $partner = Client::find($partner_id);

        if ($partner) {
            $partner->update([
                'partner_expired_at' => now()->addDays(60),
            ]);
            $partner->transactions()->create([
                'amount' => $amount * Sale::PARTNER_CASHBACK_PERCENT,
                'sale_id' => $sale_id,
                'user_id' => $user_id
            ]);
        }

        $client->fresh();

        return true;
    }

    public function getTotalCost($cart, $discount) {
        return collect($cart)->reduce(function ($a, $c) use ($discount) {
            $price = $c['product_price'] * $c['count'];
            $price = $price - ($price * max($discount, $c['discount']) / 100);
            return $a + $price;
        }, 0);
    }

    public function createCompanionTransaction(Sale $sale, $user_id) {
        if ($sale->store->type_id !== Transfer::PARTNER_SELLER_ID) {
            return true;
        }
        $saleProducts = $sale->products->pluck('product_batch_id');
        $companionSaleProducts = CompanionSaleProduct::whereIn('product_batch_id', $saleProducts)
            ->with('sale')
            ->get();

        $totalConsignmentSum = $companionSaleProducts->filter(function ($sale) {
            return $sale['sale']['is_consignment'] === true;
        })->reduce(function ($a, $c) {
            return $a + $c['product_price'] - ($c['product_price'] * $c['sale']['discount'] / 100);
        }, 0);

        if ($totalConsignmentSum > 0) {
            CompanionTransaction::create([
                'transaction_sum' => $totalConsignmentSum,
                'companion_id' => $sale->store_id,
                'user_id' => $user_id,
                'companion_sale_id' => $sale->id,
                'type' => CompanionTransaction::COMPANION_IRON_BALANCE_TYPE
            ]);
        }
    }
}
