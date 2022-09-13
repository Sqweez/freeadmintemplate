<?php

namespace App\Http\Controllers\api;

use App\Actions\Arrival\CreateArrivalAction;
use App\Actions\Arrival\SubmitArrivalAction;
use App\Actions\Arrival\UpdateArrivalAction;
use App\Arrival;
use App\ArrivalProducts;
use App\Http\Controllers\Controller;
use App\Http\Requests\Arrival\CreateArrivalRequest;
use App\Http\Requests\Arrival\UpdateArrivalRequest;
use App\Http\Resources\ArrivalForTransferResource;
use App\Http\Resources\ArrivalResource;
use App\ProductBatch;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ArrivalController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection {
        $is_completed = !!$request->get('is_completed', 0);
        return ArrivalResource::collection(
            Arrival::where('is_completed', $is_completed)
                ->with([
                    'products', 'products.product',
                    'products.product.product',
                    'products.product.product.manufacturer',
                    'products.product.product_images',
                    'products.product.attributes',
                    'products.product.attributes.attribute_name',
                    'products.product.product.attributes',
                    'products.product.product.attributes.attribute_name',
                    'user', 'store', 'bookings', 'bookings.products',
                    'products.bookingProducts'
                ])
                ->orderByDesc('created_at')
                ->get()
                ->map(function ($arrival) {
                    $products = collect($arrival->products)->filter(function ($batch) {
                        return $batch['product'] !== null;
                    });
                    unset($arrival['products']);
                    $arrival['products'] = $products;
                    return $arrival;
                }));
    }

    public function getArrivalsForTransfer(): AnonymousResourceCollection {
        $arrivals = Arrival::query()
            ->where('is_completed', true)
            ->with('products.product.product:id,product_name,product_price')
            ->latest()
            ->get();
        return ArrivalForTransferResource::collection($arrivals);
    }

    public function show(Arrival $arrival): ArrivalResource {
        return new ArrivalResource($arrival);
    }

    public function store(CreateArrivalRequest $request, CreateArrivalAction $action): ArrivalResource {
        $arrival = $action->handle($request->validated());
        $arrival->loadRelations();
        return ArrivalResource::make($arrival);
    }

    public function submit(Request $request, Arrival $arrival, SubmitArrivalAction $action): Response {
        $action->handle($request->all(), $arrival);
        return response()->noContent();
    }

    public function cancel(Arrival $arrival): Response {
        $arrival->cancel();
        ProductBatch::where('arrival_id', $arrival->id)->delete();
        return response()->noContent();
    }

    public function destroy(Arrival $arrival) {
        ArrivalProducts::destroy($arrival->products->pluck('id'));
        $arrival->delete();
    }

    public function update(UpdateArrivalRequest $request, Arrival $arrival, UpdateArrivalAction $action): ArrivalResource {
        $action->handle($request->validated(), $arrival);
        $arrival->fresh();
        $arrival->loadRelations();
        return ArrivalResource::make($arrival);
    }
}
