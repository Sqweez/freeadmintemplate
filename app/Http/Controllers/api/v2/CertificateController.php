<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use App\v2\Models\Certificate;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function create(Request $request)
    {
        return Certificate::create(
            $request->all()
        );
    }

    public function index(Request $request)
    {
        return Certificate::query()->when($request->has('active'), function ($query) {
            return $query
                ->where(function ($q) {
                    $q->whereDate('expired_at', '>=', today())
                        ->orWhere('expired_at', '=', '0000-00-00');
                })
                ->where(function ($q) {
                    $q->where('used_sale_id', 0)->orWhereNull('used_sale_id');
                });
        })
            ->get();
    }

    public function delete($id)
    {
        Certificate::find($id)->delete();
    }
}
