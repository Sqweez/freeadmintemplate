<?php

namespace App\Http\Controllers\api;

use App\Client;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClientResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        return ClientResource::collection(Client::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return ClientResource
     */
    public function store(Request $request)
    {
        $client = Client::create($request->all());
        return new ClientResource($client);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param Client $client
     * @return ClientResource
     */
    public function update(Request $request, Client $client)
    {
        $_client = $request->all();
        $client->update($_client);
        return new ClientResource($client);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Client $client
     * @return void
     * @throws \Exception
     */
    public function destroy(Client $client)
    {
        // @TODO:
        // 1.Удалить все связанные с клиентом вещи
        $client->delete();
    }
}
