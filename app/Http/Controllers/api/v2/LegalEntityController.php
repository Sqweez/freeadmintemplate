<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\api\BaseApiController;
use App\LegalEntity;
use App\Models\v2\BankAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LegalEntityController extends BaseApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->respondSuccess([
            'entities' => LegalEntity::with('bank_accounts')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $legalEntity = LegalEntity::query()
            ->create($request->all());

        return $this->respondSuccess([
            'entity' => $legalEntity
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        LegalEntity::query()
            ->whereKey($id)
            ->update($request->all());

        return $this->respondSuccess([
            'entity' => LegalEntity::whereKey($id)->first()
        ]);
    }

    public function createBankAccount(Request $request): JsonResponse
    {
        $bankAccount = BankAccount::create($request->all());
        return $this->respondSuccess([
            'account' => $bankAccount
        ]);
    }

    public function updateBankAccount(Request $request, BankAccount $account)
    {
        $account->update($request->all());
        return $this->respondSuccess([
            'account' => BankAccount::whereKey($account->id)->first()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
