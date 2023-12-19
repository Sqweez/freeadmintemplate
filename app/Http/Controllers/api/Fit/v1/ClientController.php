<?php

namespace App\Http\Controllers\api\Fit\v1;

use App\Http\Controllers\api\BaseApiController;
use App\Http\Resources\Fit\FitClientsListResource;
use App\Http\Resources\Fit\FitSingleClientResource;
use App\Models\FitClient;
use App\Repository\Fitness\FitClientRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class ClientController extends BaseApiController
{

    public function search(Request $request, FitClientRepository $clientRepository): JsonResponse
    {
        $type = $request->get('type', __hardcoded('pass'));
        $value = $request->get('value');
        $client = $clientRepository->search($type, $value);
        if (!$client) {
            return $this->respondError('По данному запросу клиентов не найдено');
        }
        $client = $client->retrieveClientResource();
        return $this->respondSuccess([
            'client' => new FitSingleClientResource($client)
        ]);
    }
    public function index(FitClientRepository $clientRepository): AnonymousResourceCollection
    {
        $clients = $clientRepository->list();
        return FitClientsListResource::collection($clients);
    }

    public function store(Request $request, FitClientRepository $clientRepository): FitSingleClientResource
    {
        $data = $request->all();
        $data['gym_id'] = auth()->user()->gym_id;
        $data['fit_user_id'] = auth()->id();
        $data['phone'] = unmask_phone($data['phone']);
        $client = $clientRepository->create($data);
        $client = $client->retrieveClientResource();
        return new FitSingleClientResource($client);
    }

    public function update(FitClient $client, Request $request, FitClientRepository $clientRepository): FitSingleClientResource
    {
        $data = $request->except(['photo']);
        if ($request->file('photo')) {
            $data['photo'] = $request->file('photo')->store('public/fit/clients');
        }
        if ($client->photo) {
            Storage::delete($client->photo);
        }
        $client = $clientRepository->update($client, $data);
        $client = $client->retrieveClientResource();
        return new FitSingleClientResource($client);
    }

    public function topUpBalance(FitClient $client, Request $request): JsonResponse
    {
        $client->topUp($request->all());
        $client = $client->retrieveClientResource();
        return $this->respondSuccess([
            'client' => new FitSingleClientResource($client)
        ]);
    }
}
