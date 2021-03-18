<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use App\v2\Models\Certificate;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function create(Request $request) {
        return Certificate::create(
            $request->all()
        );
    }

    public function index() {
        return Certificate::all();
    }

    public function delete($id) {
        Certificate::find($id)->delete();
    }
}
