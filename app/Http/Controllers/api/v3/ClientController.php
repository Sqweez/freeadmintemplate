<?php

namespace App\Http\Controllers\api\v3;

use App\DTO\Filters\ClientFilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use App\Repository\ClientRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClientController extends Controller
{

    private ClientRepository $clientRepository;

    public function __construct()
    {
        $this->clientRepository = app(ClientRepository::class);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        return ClientResource::collection(
            $this->clientRepository
                ->query(new ClientFilterDTO($request->all()))
                ->paginate($request->get('per_page', 10))
        );
    }

    public function search()
    {

    }
}
