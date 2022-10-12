<?php

namespace App\Http\Controllers\api\v2;

use App\Actions\Reports\GetBrandSalesRatingAction;
use App\Actions\Reports\GetPlanReportsAction;
use App\Actions\Reports\GetProductSalesRating;
use App\Actions\Reports\GetSellersByMarginTypes;
use App\Actions\Reports\GetSellersByPaymentTypeAction;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function getPlanReports(Request $request, GetPlanReportsAction $action): array {
        return $action->handle($request->get('store_id', 1));
    }

    public function getBrandsRating(Request $request, GetBrandSalesRatingAction $action) {
        $start = Carbon::parse($request->get('start', now()));
        $finish = Carbon::parse($request->get('finish', now()));
        $store = intval($request->get('store', -1));
        return $action->handle($start, $finish, $store);
    }

    public function getSellersByPaymentTypes(Request $request, GetSellersByPaymentTypeAction $action) {
        $start = Carbon::parse($request->get('start', now()));
        $finish = Carbon::parse($request->get('finish', now()));
        $payment_type = intval($request->get('payment_type', -1));
        return $action->handle($start, $finish, $payment_type);
    }

    public function getSellersByMarginTypes(Request $request, GetSellersByMarginTypes $action) {
        $start = Carbon::parse($request->get('start', now()));
        $finish = Carbon::parse($request->get('finish', now()));
        $margin_type = intval($request->get('margin_type', -1));
        return $action->handle($start, $finish, $margin_type);
    }

    public function getProductsRating(Request $request, GetProductSalesRating $action) {
        $start = Carbon::parse($request->get('start', now()));
        $finish = Carbon::parse($request->get('finish', now()));
        $store_id = intval($request->get('store_id', -1));
        return $action->handle($start, $finish, $store_id);
    }
}
