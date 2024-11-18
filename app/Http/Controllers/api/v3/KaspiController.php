<?php

namespace App\Http\Controllers\api\v3;

use App\Http\Controllers\api\BaseApiController;
use App\Http\Resources\Kaspi\KaspiOrderResource;
use App\Service\Kaspi\KaspiOrdersApiService;
use App\Store;
use App\v2\Models\KaspiEntity;
use App\v2\Models\KaspiEntityStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class KaspiController extends BaseApiController
{
    public function getEntities(): JsonResponse
    {
        return $this->respondSuccess([
            'kaspi_entities' => KaspiEntity::all(),
        ]);
    }

    public function retrieveOrders(Request $request, KaspiOrdersApiService $apiService): JsonResponse
    {
        $response = $apiService->getOrders(
            $request->get('page', 0),
            json_decode($request->get('filters'), true) ?: []
        );
        $pickupPoints = $this->checkPickupPointExisting($response['included'], $apiService);
        $stores = Store::whereIn('id', $pickupPoints->values()->pluck('store_id')->toArray())->select(['id', 'name'])->get();
        KaspiOrderResource::setAdditionalData($pickupPoints, collect($response['included']), $stores);
        return $this->respondSuccess([
            'success' => $response['success'],
            'meta' => $response['meta'],
            'data' => KaspiOrderResource::collection($response['data']),
        ]);
    }

    private function checkPickupPointExisting(array $includes, KaspiOrdersApiService $apiService): Collection
    {
        $pickupPointIds = collect($includes)->pluck('relationships.deliveryPointOfService.data.id')->unique()->values();
        $pickupPoints = KaspiEntityStore::whereIn('kaspi_external_id', $pickupPointIds->toArray())->get();
        $diff = $pickupPointIds->diff($pickupPoints->pluck('kaspi_external_id'))->values();
        if ($diff->isNotEmpty()) {
            foreach ($diff as $item) {
                $data = $apiService->getPointOfService($item)['data'];
                $kaspiEntityStore = KaspiEntityStore::query()
                    ->where('kaspi_store_id', substr($data['attributes']['displayName'], 2))
                    ->first();
                if ($kaspiEntityStore) {
                    $kaspiEntityStore->update([
                        'kaspi_external_id' => $data['id'],
                        'address' => $data['attributes']['address'],
                    ]);
                }
            }
            $pickupPoints = KaspiEntityStore::whereIn('kaspi_external_id', $pickupPointIds->toArray())->get();;
        }
        return $pickupPoints->map(function (KaspiEntityStore $point) {
            return [
                'key' => sprintf("IronAddicts_PP%s", $point->kaspi_store_id),
                'address' => $point->address['formattedAddress'],
                'store_id' => $point->store_id
            ];
        })->keyBy('key');
    }

    public function retrieveOrderById(string $kaspiId, KaspiOrdersApiService $apiService): JsonResponse
    {
        $response = $apiService->getOrderById($kaspiId);
        return $this->respondSuccess($response);
    }

    public function retrievePointOfService($id, KaspiOrdersApiService $apiService): JsonResponse
    {
        $response = $apiService->getPointOfService($id);
        return $this->respondSuccess($response);
    }
}
