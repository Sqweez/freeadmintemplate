<?php

namespace App\Http\Controllers\api;

use App\Client;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\SingleTransferResource;
use App\Http\Resources\TransferResource;
use App\Product;
use App\ProductBatch;
use App\Transfer;
use App\TransferBatch;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransferController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request) {
        $mode = $request->get('mode');
        $transfers = Transfer::where('is_confirmed', !($mode === 'current'))
            ->with(['parent_store', 'child_store'])
            ->with(['batches' => function ($query) {
                return $query
                    ->leftJoin('product_batches', 'product_batches.id', '=', 'transfer_batches.batch_id')
                    ->leftJoin('products', 'products.id', '=', 'transfer_batches.product_id')
                    ->select('transfer_batches.*', 'product_batches.purchase_price as purchase_price', 'products.product_price as product_price');
            }])
            ->select('id', 'parent_store_id', 'child_store_id', 'user_id', 'photos')
            ->get();
        return TransferResource::collection($transfers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return array
     */
    public function store(Request $request) {
        $_transfer = $request->except('cart');
        $transfer = Transfer::create($_transfer);
        $transfer_id = $transfer['id'];
        $store_id = $request->get('parent_store_id');
        $cart = $request->get('cart');
        $this->parseCart($transfer_id, $store_id, $cart);
        return ['products' => ProductResource::collection(Product::find(array_map(function ($i) {
            return $i['id'];
        }, $cart))),];
    }

    private function parseCart($id, $store_id, $cart = []) {
        foreach ($cart as $item) {
            for ($i = 0; $i < $item['count']; $i++) {
                $product_batch = ProductBatch::where('product_id', $item['id'])->where('store_id', $store_id)->where('quantity', '>=', 1)->first();
                $batch_transfer = ['batch_id' => $product_batch['id'], 'product_id' => $item['id'], 'transfer_id' => $id,];
                $this->decreaseCount($product_batch);
                TransferBatch::create($batch_transfer);
            };
        }
    }

    private function decreaseCount(ProductBatch $productBatch) {
        $quantity = $productBatch['quantity'] - 1;
        $productBatch->update(['quantity' => $quantity]);
    }

    /**
     * Display the specified resource.
     *
     * @param Transfer $transfer
     * @return SingleTransferResource
     */
    public function show($transfer) {
        return new SingleTransferResource(
            Transfer::where('id', $transfer)
                ->with(['batches', 'batches.product', 'batches.product.manufacturer', 'batches.product.attributes', 'batches.product.attributes.attribute_name'])
                ->first()
        );
    }


    public function acceptTransfer(Request $request, Transfer $transfer) {
        $products = $request->all();
        $this->makeTransfer($transfer, $products);
        $this->returnGoods($transfer);
        $transfer->update(['is_confirmed' => true]);
    }

    public function declineTransfer(Request $request, Transfer $transfer) {
        $this->returnGoods($transfer);
        TransferBatch::where('transfer_id', $transfer['id'])->delete();
        $transfer->delete();
    }

    private function makeTransfer($transfer, $products) {
        $batches = $transfer->batches;
        $grouped_batches = $this->groupBatches($batches);
        foreach ($products as $product) {
            $id = $product['product_id'];
            $needle_batches = array_filter($grouped_batches, function ($i) use ($id) {
                return $i['product_id'] === $id;
            });
            $_count = $product['count'];
            foreach ($needle_batches as $needle_batch) {
                $batch = ProductBatch::find($needle_batch['batch_id']);
                $quantity = 0;
                $_needle_count = $needle_batch['count'];
                while ($_needle_count > 0 && $_count > 0) {
                    $quantity++;
                    --$_needle_count;
                    --$_count;
                    TransferBatch::where('batch_id', $needle_batch['batch_id'])->where('is_transferred', false)->where('transfer_id', $transfer['id'])->first()->update(['is_transferred' => true]);
                }
                if ($quantity > 0) {
                    ProductBatch::create(['product_id' => $batch['product_id'], 'quantity' => $quantity, 'store_id' => $transfer['child_store_id'], 'purchase_price' => $batch['purchase_price']]);
                }

            }
        }
    }

    public function returnGoods(Transfer $_transfer) {
        $transfer = Transfer::find($_transfer['id']);
        $_batches = $transfer->batches->where('is_transferred', false);
        $_batches = $this->groupBatches($_batches);
        foreach ($_batches as $_batch) {
            $batch_id = $_batch['batch_id'];
            $batch = ProductBatch::find($batch_id);
            $quantity = $batch['quantity'] + $_batch['count'];
            $batch->update(['quantity' => $quantity]);
        }
    }

    private function groupBatches($batches) {
        $_batches = [];
        foreach ($batches as $value) {
            $_batches[$value['batch_id']][] = $value;
        }
        $result = [];

        foreach ($_batches as $product) {
            array_push($result, ['count' => count($product), 'batch_id' => $product[0]['batch_id'], 'product_id' => $product[0]['product_id']]);
        }

        return $result;
    }

    private function createBatch() {

    }

    private function updateBatch() {

    }
}
