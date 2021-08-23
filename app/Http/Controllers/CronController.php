<?php

namespace App\Http\Controllers;

use App\Client;
use App\Promocode;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

    public function revokeSellerToken() {
        $users = User::query()
            ->where('role_id', 2)
            ->orWhere('id', 29)
            ->get();
        return $users->each(function (User $user) {
            return $user->update(['token' => Str::random(60)]);
        });
    }
}
