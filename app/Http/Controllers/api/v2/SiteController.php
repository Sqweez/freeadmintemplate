<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use App\v2\Models\Footer;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function getFooter() {
        return Footer::all()->first();
    }

    public function editFooter(Request $request) {
        Footer::truncate();
        Footer::create($request->all());
    }
}
