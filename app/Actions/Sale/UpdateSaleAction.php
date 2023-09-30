<?php

namespace App\Actions\Sale;

use App\ClientTransaction;
use App\Http\Controllers\Services\SaleService;
use App\ProductBatch;
use App\Sale;
use App\SaleProduct;
use Illuminate\Http\Request;

class UpdateSaleAction {

    private SaleService $saleService;

    public function __construct(SaleService $saleService) {
        $this->saleService = $saleService;
    }

    public function handle(Sale $sale, Request $request) {
        \DB::beginTransaction();
        $saleProducts = $sale->products;
        // 1. Удаляем старый состав заказа и возвращаем на склад
        $saleProducts->each(function ($product) use ($sale) {
            ProductBatch::query()
                ->whereId($product['product_batch_id'])
                ->increment('quantity');
        });
        SaleProduct::query()
            ->whereSaleId($sale->id)
            ->delete();
        // 2. Удаляем клиентскую транзакцию, если она была
        if ($sale->client_id !== -1) {
            ClientTransaction::whereSaleId(($sale->id))->delete();
        }
        // 3. Обновляем поля продажи
        $sale->update([
            'client_id' => $request->get('client_id', -1),
            'discount' => $request->get('discount', 0),
            // Выглядит как сайд-эффект, сделано для оптовых продаж
            'is_confirmed' => true
        ]);

        $sale->fresh();
        // 4. Заново создаем список продуктов
        $cart = $request->get('cart');
        $this->saleService->createSaleProducts($sale, $sale->store_id, $cart);
        $this->saleService->createClientSale($sale->client_id, $sale->discount, $cart, $sale->balance, $sale->user, $sale->id, $sale->partner_id, $sale->payment_type);
        $sale->fresh();
        \DB::commit();
    }
}
