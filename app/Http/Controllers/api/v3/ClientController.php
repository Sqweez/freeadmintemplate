<?php

namespace App\Http\Controllers\api\v3;

use App\DTO\Filters\ClientFilterDTO;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use App\Repository\ClientRepository;
use App\Services\Clients\ClientExportService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClientController extends Controller
{

    private ClientRepository $clientRepository;

    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
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

    public function export(Request $request, ClientExportService $service): StreamedResponse
    {
        $query = $this->clientRepository->queryForExport(
            new ClientFilterDTO($request->all())
        );

        return $service->download($query);
    }
}
