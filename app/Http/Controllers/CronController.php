<?php

namespace App\Http\Controllers;

use App\Client;
use App\Promocode;
use Illuminate\Http\Request;

class CronController extends Controller
{
    public function disablePartners(Request $request) {
        $clients = Client::Partner()->whereDate('partner_expired_at', now())->pluck('id');
        Promocode::OfPartner($clients)->update(
            [
                'is_active' => false
            ]
        );

        Client::where('id', $clients)->update(
            [
                'is_partner' => false
            ]
        );
    }
}
