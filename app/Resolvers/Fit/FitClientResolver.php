<?php

namespace App\Resolvers\Fit;

use App\Concerns\UseQuickBindings;
use App\Models\FitClient;

class FitClientResolver
{

    use UseQuickBindings;

    public function resolve(FitClient $client): FitClient
    {
        $client->load('purchased_services.service');
        $client->load('purchased_services.visits');
        return $client;
    }
}
