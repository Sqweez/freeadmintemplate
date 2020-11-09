<?php

namespace App\Http\Controllers;

use App\Client;
use Illuminate\Http\Request;

class HelpController extends Controller
{
    public function setPartnerExpiredAt() {
        return Client::Partner()->update(
            [
                'partner_expired_at' => now()->addDays(60)
            ]
        );
    }
}
