<?php

namespace App\Http\Controllers\api\Fit\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Fit\FitClientsListResource;
use App\Repository\Fitness\FitClientRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClientController extends Controller
{

    public function search(Request $request, FitClientRepository $clientRepository): ?FitClientsListResource
    {
        $type = $request->get('type', __hardcoded('pass'));
        $value = $request->get('value');
        $client = $clientRepository->search($type, $value);
        return new FitClientsListResource($client);
    }
    public function index(FitClientRepository $clientRepository): AnonymousResourceCollection
    {
        $clients = $clientRepository->list();
        return FitClientsListResource::collection($clients);
    }

    public function store(Request $request, FitClientRepository $clientRepository): FitClientsListResource
    {
        $data = $request->all();
        $data['gym_id'] = auth()->user()->gym_id;
        $data['fit_user_id'] = auth()->id();
        $data['phone'] = unmask_phone($data['phone']);
        $client = $clientRepository->create($data);
        return new FitClientsListResource($client);
    }
}
