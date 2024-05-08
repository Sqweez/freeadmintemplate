<?php

namespace App\Http\Controllers\api\v2;

use App\DTO\Filters\TransferFilterDTO;
use App\Http\Controllers\api\BaseApiController;
use App\Http\Resources\TransferResource;
use App\Repository\TransferRepository;
use App\User;
use Illuminate\Http\Request;

class TransferController extends BaseApiController
{
    public function index(Request $request, TransferRepository $transferRepository)
    {
        $filters = new TransferFilterDTO($request->all());
        /* @var User $user */
        $user = auth()->user();
        $transferQuery = $transferRepository->get($filters, $user);
        return TransferResource::collection(
            $transferQuery->paginate($request->get('per_page', 5))
        );
    }
}
