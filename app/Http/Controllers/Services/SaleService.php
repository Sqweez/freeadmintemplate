<?php


namespace App\Http\Controllers\Services;


use App\Client;
use App\ClientTransaction;
use App\CompanionSaleProduct;
use App\CompanionTransaction;
use App\ProductBatch;
use App\Promocode;
use App\Sale;
use App\Transfer;
use App\v2\Models\ProductSku;
use Carbon\Carbon;

class SaleService {

    public function createSale($sale): Sale{
        // @TODO 2022-07-02T20:16:42 hardcoded изменять оплату на Андрея при каспи магазине
        if (in_array($sale['payment_type'], __hardcoded([4, 8]))) {
            $sale['user_id'] = __hardcoded(32);
        }
        if (isset($sale['is_opt']) && $sale['is_opt']) {
            $sale['is_confirmed'] = false;
        }
        if (isset($sale['custom_sale_date'])) {
            $sale['created_at'] = Carbon::parse($sale['custom_sale_date'])->addHours(22);
            unset($sale['custom_sale_date']);
        }
        if ($sale['promocode_id']) {
            $promocode = Promocode::find($sale['promocode_id']);
            if ($promocode->promocode_type_id === 2) {
                $sale['promocode_fixed_amount'] = $promocode->discount;
            }
        }
        return Sale::create($sale)->refresh();
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


    public function createClientSale($client_id, $discount, $cart, $balance, $user_id, $sale_id, $partner_id, $payment_type = 1): bool {
        $client = Client::find($client_id);
        if ($client) {
            $amountWithOutDiscount = $this->getTotalCost($cart, 0);
            $newDiscount = 0;
            if ($amountWithOutDiscount >= 15000) {
                $newDiscount = 5;
            }
            if ($amountWithOutDiscount >= 30000) {
                $newDiscount = 10;
            }

            if ($newDiscount > $client->client_discount) {
                $client->update(['client_discount' => $newDiscount]);
            }

            $amount = $this->getTotalCost($cart, $discount);

            $client->sales()->create([
                'sale_id' => $sale_id,
                'amount' => $amount
            ]);

            $client->cached_total_sale_amount += $amount;
            $client->save();

            $cashbackPercent = $payment_type === 3 ? 5 : $client->loyalty->cashback;

            $client->transactions()->create([
                'sale_id' => $sale_id,
                'user_id' => $user_id,
                'amount' => $amount * ($cashbackPercent / 100)
            ]);

            $client->cached_balance += $amount * ($cashbackPercent / 100);
            $client->save();

            if ($balance > 0) {
                $client->transactions()->create([
                    'sale_id' => $sale_id,
                    'user_id' => $user_id,
                    'amount' => $balance * -1
                ]);
                $client->cached_balance -= $balance;
                $client->save();
            }
        }


        $partner = Client::find($partner_id);

        if ($partner) {
            $partner->update([
                'partner_expired_at' => now()->addDays(60),
            ]);

            $store = Sale::find($sale_id)->store;

            #$partnerSalesAmount = $this->getPartnerSalesAmount($partner, $sale_id);


            $partnerIDS = [16292, 12952, 11966, 12604, 14072, 12780, 12569, 14308];

            $partnerCashback = collect($cart)
                ->reduce(function ($a, $c) use ($store, $discount, $partner, $partnerIDS) {
                    $price = $c['product_price'] * $c['count'];
                    $finalPrice = $price - ($price * max($discount, $c['discount']) / 100);
                    $cashbackPercent = 0.05;
                    if (in_array($partner->id, $partnerIDS)) {
                        $cashbackPercent = 0.1;
                    }
                    //$cashbackPercent = floatval($store->partner_cashback_percent) / 100;
                    return $a + ($finalPrice * $cashbackPercent);
                });
            #$partnerCashback = $this->calculatePartnerCashback($cart, $partnerSalesAmount, $discount);

            $partner->transactions()->create([
                'amount' => $partnerCashback,
                'sale_id' => $sale_id,
                'user_id' => $user_id,
                'type_id' => ClientTransaction::TYPE_PARTNER_ROYALTY,
            ]);

            $partner->cached_balance += $partnerCashback;
            $partner->save();
        }

        if ($client) {
            $client->fresh();
        }

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

    public function calculateSaleFinalAmount($sale): int {
        return collect($sale)->reduce(function ($a, $c) {
            $productCost = collect($c['products'])->reduce(function ($_a, $_c) {
                return $_a + ($_c['product_price'] - ($_c['product_price'] * $_c['discount'] / 100));
            }, 0);
            if ($c['kaspi_red']) {
                $productCost -= $productCost * Sale::KASPI_RED_PERCENT;
            }
            if ($c['balance'] > 0) {
                $productCost  -= $c['balance'];
            }
            return $a + $productCost;
        }, 0);
    }

    private function getPartnerSalesAmount(Client $client, $sale_id): int {
        $sales = Sale::query()
            ->wherePartnerId($client->id)
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->with('products')
            ->where('id', '!=', $sale_id)
            ->get();

        return $this->calculateSaleFinalAmount($sales);
    }

    private function calculatePartnerCashback($cart, $amount, $discount): int {
        $sku = ProductSku::query()
            ->with('margin_type')
            ->whereIn('id', collect($cart)->pluck('id'))
            ->get();
        return floor(collect($cart)->map(function ($item) use ($sku) {
            $needle = collect($sku)->where('id', $item['id'])->first();
            $item['margin_type'] = $needle['margin_type'];
            return $item;
        })->reduce(function ($a, $c) use ($amount, $discount) {
            $price = $c['product_price'] * $c['count'];
            $finalPrice = $price - ($price * max($discount, $c['discount']) / 100);
            $cashbackRule = collect($c['margin_type']['partner_cashback_rules'])
                    ->sortByDesc('threshold')
                    ->values()
                    ->filter(function ($rule) use ($amount) {
                        return $rule['threshold'] <= $amount;
                    })
                    ->first()['value'] ?? 0;
            $cashbackPercent = $cashbackRule / 100;
            return $a + ($finalPrice * $cashbackPercent);
        }, 0));
    }
}
