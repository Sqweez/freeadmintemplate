<?php

namespace App\Http\Controllers\api\Fit\v1;

use App\Http\Controllers\api\BaseApiController;
use App\Http\Resources\Fit\FitServiceListResource;
use App\Http\Resources\Fit\FitSingleClientResource;
use App\Models\FitClient;
use App\Models\FitService;
use App\Models\FitServiceSale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends BaseApiController
{
    public function index()
    {
        $services = FitService::query()
            ->where('gym_id', auth()->user()->gym_id)
            ->get();

        return $this->respondSuccess([
            'services' => FitServiceListResource::collection($services)
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $service = FitService::create($request->all() + ['gym_id' => auth()->user()->gym_id]);
        return $this->respondSuccess([
            'service' => FitServiceListResource::make($service)
        ]);
    }

    public function update(FitService $service, Request $request): JsonResponse
    {
        $service->update($request->all());
        return $this->respondSuccess([
            'service' => FitServiceListResource::make($service)
        ]);
    }

    public function createSale(Request $request): JsonResponse
    {
        $client = FitClient::find($request->get('client_id'));
        $data = $request->all();
        $transaction = $client->topUp([
            'type' => $data['payment_type'],
            'amount' => $data['price'] * - 1,
            'description' => sprintf('Покупка услуги %s', $data['name']),
        ]);
        FitServiceSale::create($data + ['transaction_id' => $transaction->id]);
        $client = $client->retrieveClientResource();
        return $this->respondSuccess([
            'client' => new FitSingleClientResource($client)
        ]);
    }

    public function createVisit(FitServiceSale $sale, Request $request): JsonResponse
    {
        if (!$sale->is_activated) {
            $sale->activate();
        }
        if (!$sale->getCanBeUsed()) {
            return $this->respondError(['Тренировка не может быть списана']);
        }
        $sale->visits()->create($request->all());
        $client = FitClient::find($sale->client_id);
        $client->retrieveClientResource();
        return $this->respondSuccess([
            'client' => new FitSingleClientResource($client)
        ]);
    }
}
