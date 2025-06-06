<?php

namespace App\Http\Controllers\api;

use App\CompanionSale;
use App\CompanionSaleProduct;
use App\CompanionTransaction;
use App\Http\Controllers\Controller;
use App\Http\Resources\SingleTransferResource;
use App\Http\Resources\TransferResource;
use App\ProductBatch;
use App\Store;
use App\Transfer;
use App\TransferBatch;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransferController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection {
        $mode = $request->get('mode');
        /* @var User $user */
        $user = auth()->user();
        $transfersQuery = Transfer::query();
        $transfersQuery = $transfersQuery
            ->whereHas('batches.product')
            ->whereHas('batches.product.product')
            ->with(['parent_store', 'child_store', 'companionSale'])
            ->with(['user:id,name'])
            ->with(
                [
                    'batches', 'batches.productBatch:id,purchase_price',
                    'batches.product:id,product_id,self_price',
                    'batches.product.product:id,product_price,product_name',
                    'batches.product.product.manufacturer',
                    'batches.product.product.attributes', 'batches.product.attributes',
                    'batches.product.product.attributes.attribute_name'
                ])
            ->select(['id', 'parent_store_id', 'child_store_id', 'user_id', 'photos', 'created_at', 'updated_at', 'is_confirmed', 'is_accepted'])
            ->when($mode === 'current', function ($query) {
                return $query
                    ->where('is_confirmed', false)
                    ->where('is_accepted', true);
            })
            ->when($mode === 'history', function ($query) {
                return $query
                    ->where('is_confirmed', true)
                    ->where('is_accepted', true)
                    ->whereDate('updated_at', '>=', today()->subDays(30)->startOfDay());
            })
            ->when($mode === 'not_accepted', function ($query) {
                return $query
                    ->where('is_accepted', false);
            })
            ->when($request->has('partners'), function ($q) {
                return $q->whereHas('child_store', function ($q) {
                    return $q->where('type_id', Transfer::PARTNER_SELLER_ID);
                });
            })
            ->when($user && $user->isSeller(), function ($q) use ($user) {
                return $q
                    ->where(function ($q) use ($user) {
                        return $q->where('child_store_id', $user->store_id)
                            ->orWhere('parent_store_id', $user->store_id);
                    });
            })
            ->when($user && $user->isFranchise(), function ($q) use ($user) {
                $store = Store::find($user->store_id);
                $city_id = $store->city_id;
                $store_ids = Store::where('city_id', $city_id)->get()->pluck('id');
                return $q
                    ->where(function ($q) use ($store_ids) {
                        return $q->whereIn('child_store_id', $store_ids->toArray())
                            ->orWhereIn('parent_store_id', $store_ids->toArray());
                    });
            })
            ->orderByDesc('created_at');

        return TransferResource::collection($transfersQuery->get());
    }

    public function update(Transfer $transfer, Request $request) {
        $transfer->update($request->all());
        return new TransferResource($transfer);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|Response
     */
    public function store(Request $request) {
        $_transfer = $request->except(['cart', 'discount', 'is_consignment']);
        $_transfer['is_accepted'] = true;
        $transfer = Transfer::create($_transfer);
        $store_id = $request->get('parent_store_id');
        $cart = $request->get('cart');
        $discount = $request->get('discount');
        $this->parseCart($transfer, $store_id, $discount, $cart);
        $isPartnerTransfer = $transfer->child_store->type_id === Transfer::PARTNER_SELLER_ID;
        if ($isPartnerTransfer) {
            return $this->createPartnerTransfer($transfer, $request);
        }
        return response([], 200);
    }

    private function parseCart(Transfer $transfer, $store_id, $discount, $cart = []) {
        foreach ($cart as $item) {
            for ($i = 0; $i < $item['count']; $i++) {
                $product_batch = ProductBatch::where('product_id', $item['id'])->where('store_id', $store_id)->where('quantity', '>=', 1)->first();
                $transfer->batches()->create([
                    'batch_id' => $product_batch['id'],
                    'product_id' => $item['id'],
                    'discount' => max($discount, $item['discount'])
                ]);
                $product_batch->decrement('quantity');
                $product_batch->save();
            };
        }
    }

    private function createPartnerTransfer(Transfer $transfer, Request $request) {
        $isConsignment = $request->has('is_consignment') ? !!$request->get('is_consignment') : false;

        $companionSale = CompanionSale::create([
            'store_id' => $transfer->parent_store_id,
            'companion_id' => $transfer->child_store_id,
            'user_id' => $request->header('user_id'),
            'discount' => $request->get('discount'),
            'is_consignment' => $isConsignment,
            'transfer_id' => $transfer->id,
        ]);

        $batches = TransferBatch::with('product', 'product.product', 'productBatch')
            ->whereTransferId($transfer->id)
            ->get();

        $batches->each(function ($batch) use ($companionSale) {
            CompanionSaleProduct::create([
                'product_batch_id' => $batch['batch_id'],
                'product_id' => $batch['product_id'],
                'companion_sale_id' => $companionSale->id,
                'purchase_price' => $batch['productBatch']['purchase_price'],
                'product_price' => $batch['product']['product']['product_price'],
                'discount' => $batch['discount']
            ]);
        });

        if ($isConsignment) {
            return $transfer;
        }

        $totalSum = $batches->reduce(function ($a, $c) {
            $cost = intval($c['product']['product']['product_price']);
            return $a + ($cost - ($cost * $c['discount'] / 100));
        }, 0);


        CompanionTransaction::create([
            'transaction_sum' => $totalSum * -1,
            'companion_id' => $transfer->child_store_id,
            'user_id' => \request()->header('user_id'),
            'companion_sale_id' => $transfer->id,
        ]);

        return $transfer;
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
                ->with([
                    'batches',
                    'batches.product', 'batches.product.product.manufacturer', 'batches.product.product.attributes', 'batches.product.attributes', 'batches.product.product.attributes.attribute_name', 'batches.product.attributes.attribute_name',])
                ->first()
        );
    }


    public function acceptTransfer(Request $request, Transfer $transfer) {
        $products = $request->all();
        $this->makeTransfer($transfer, $products);
        $this->returnGoods($transfer);
        $transfer->update(['is_confirmed' => true]);
    }

    public function confirmTransfer(Request $request, Transfer $transfer) {
        $transfer->update(['is_accepted' => true]);
        $products = $request->all();
        $batches = $transfer->batches;
        $groupedBatches = $this->groupBatches($batches);
        foreach ($groupedBatches as $key => $batch) {
            $product_id = $batch['product_id'];
            $needle = collect($products)->filter(function ($i) use ($product_id) {
                return $i['product_id'] === $product_id;
            })->first() ?? [];
            $countDifference = count($needle) === 0 ? $batch['count'] : $batch['count'] - $needle['count'];
            if ($countDifference > 0) {
                $batches->filter(function ($i) use ($product_id) {
                    return $i['product_id'] === $product_id;
                })
                    ->take($countDifference)->each(function ($batch) {
                        $productBatch = ProductBatch::find($batch['batch_id']);
                        $productBatch->update(['quantity' => $productBatch->quantity + 1]);
                        TransferBatch::find($batch['id'])->delete();
                    });
            }
        }
    }

    public function declineTransfer(Request $request, Transfer $transfer) {
        $this->returnGoods($transfer);
        TransferBatch::where('transfer_id', $transfer['id'])->delete();
        $transfer->delete();
    }

    private function makeTransfer(Transfer $transfer, $products) {
        $batches = $transfer->batches->where('is_transferred', 0);
        $companionSale = CompanionSale::where('transfer_id', $transfer->id)->first();
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
                    TransferBatch::where('batch_id', $needle_batch['batch_id'])
                        ->where('is_transferred', false)
                        ->where('transfer_id', $transfer['id'])
                        ->first()
                        ->update(['is_transferred' => true]);
                }
                if ($quantity > 0) {
                    $product_batch = ProductBatch::create(
                        [
                            'product_id' => $batch['product_id'],
                            'quantity' => $quantity,
                            'store_id' => $transfer['child_store_id'],
                            'purchase_price' => $batch['purchase_price']
                        ]);

                    if ($companionSale) {
                        CompanionSaleProduct::where('product_batch_id', $needle_batch['batch_id'])
                            ->where('companion_sale_id', $companionSale->id)
                            ->update([
                                'product_batch_id' => $product_batch->id
                            ]);
                    }
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

    public function updateTransfer(Request $request) {
        $cart = $request->get('cart');
        $transfer = Transfer::find($request->get('id'));
        $this->parseCart($transfer, $transfer->parent_store_id, 0, $cart);
        return \response([], 200);
    }

    private function createBatch() {

    }

    private function updateBatch() {

    }
}
