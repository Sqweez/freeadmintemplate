<?php


namespace App\Http\Controllers\Services;


use App\Http\Resources\v2\Report\ReportsResource;
use App\Sale;
use App\Store;
use App\User;
use App\v2\Models\Supplier;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ReportService {
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function getReports($start, $finish, $user_id, $is_supplier = false, $store_id = null, $manufacturer_id = null): AnonymousResourceCollection {
        $saleQuery = Sale::query();
        $sales = null;
        $user = null;
        /* @var User $authUser */
        $authUser = auth()->user();
        if (!$is_supplier) {
            $saleQuery = $saleQuery->report()->reportDate([$start, $finish])->with('promocode');
            if ($user_id) {
                $user = User::find($user_id);
                $saleQuery->whereStoreId($user->store_id);
            }
            if ($store_id) {
                $saleQuery->whereStoreId($store_id);
            }

            if (!$authUser->is_super_user) {
                $saleQuery->where('is_confirmed', true);
            }

            /*if ($authUser->isStoreKeeper()) {
                $saleQuery
                    ->where('payment_type', __hardcoded(4));
            }*/

            if ($authUser->isFranchise()) {
                $store = Store::find($authUser->store_id);
                $storeIds = Store::where('city_id', $store->city_id)->get()->pluck('id');
                $saleQuery->whereIn('store_id', $storeIds);
            }

            if (request()->has('promocode_id')) {
                $saleQuery->where('promocode_id', json_decode(request()->get('promocode_id')));
            }

            $sales = $saleQuery->get();
        } else {
            $_supplier = Supplier::where('user_id', $user_id)->first();
            $supplierProducts = $_supplier->products->pluck('id')->toArray();
            $saleQuery = $saleQuery->reportDate([$start, $finish])->reportSupplier($supplierProducts);
            $supplierProducts = collect($supplierProducts);
            $sales = $saleQuery->get()->map(function ($sale) use ($supplierProducts) {
                $products = $sale['products'];
                unset($sale['products']);
                $products = collect($products)->filter(function ($product) use ($supplierProducts) {
                    return $supplierProducts->contains($product['product']['product_id']);
                });
                $sale['products'] = $products;
                return $sale;
            });
        }

        // TODO 2022-03-28T22:08:31 уродливо, но работает

        if ($manufacturer_id) {
            $sales = $sales->map(function ($sale) use ($manufacturer_id) {
                $products = $sale['products'];
                unset($sale['products']);
                $products = collect($products)->filter(function ($p) use ($manufacturer_id) {
                    return $p['product']['product']['manufacturer_id'] === intval($manufacturer_id);
                });
                $sale['products'] = $products;
                return $sale;
            })->filter(function ($sale) {
                return count($sale['products']) > 0;
            })->values();
        }
        return ReportsResource::collection($sales);
    }

    public static function getClientReports($client_id): AnonymousResourceCollection {
        $saleQuery = Sale::query();
        $saleQuery = $saleQuery->report()->whereClientId($client_id)->orderByDesc('created_at');

        return ReportsResource::collection($saleQuery->get());
    }
}
