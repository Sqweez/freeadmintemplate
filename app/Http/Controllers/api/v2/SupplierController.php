<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use App\v2\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request) {
        return Supplier::with('user')->get();
    }

    public function store(Request $request) {
        $supplier = $request->only(['user_id', 'supplier_name']);
        $supplier = Supplier::create($supplier);
        return Supplier::with('user')->whereKey($supplier->id)->first();
    }

    public function update($id, Request $request) {
        $supplier = Supplier::find($id);
        $supplier->update($request->only(['supplier_name', 'user_id']));
        return Supplier::with('user')->whereKey($supplier->id)->first();
    }

    public function destroy($id) {
        Supplier::find($id)->delete();
    }
}
