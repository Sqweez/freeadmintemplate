<?php

namespace App\Repository\Fitness;

use App\Concerns\UseQuickBindings;
use App\Models\FitClient;
use App\Models\FitUser;

class FitClientRepository
{
    use UseQuickBindings;

    public function search($type, $value): ?FitClient
    {
        $user = auth()->user();
        return FitClient::query()
            ->where('gym_id', $user->gym_id)
            ->where($type, $value)
            ->first();
    }

    public function list()
    {
        /* @var FitUser $user */
        $user = auth()->user();
        return FitClient::query()
            ->where('gym_id', $user->gym_id)
            ->get();
    }

    public function create($data)
    {
        return FitClient::create($data);
    }

    public function update(FitClient $client, $data): FitClient
    {
        $client->update($data);
        $client->fresh();
        return $client;
    }
}
