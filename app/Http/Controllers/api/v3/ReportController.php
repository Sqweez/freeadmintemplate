<?php

namespace App\Http\Controllers\api\v3;

use App\Actions\Reports\CollectProductReportAction;
use App\Http\Controllers\api\BaseApiController;
use Illuminate\Http\Request;

class ReportController extends BaseApiController
{
    public function getProductReport(Request $request) {
        $action = CollectProductReportAction::i();
        $response = $action->handle($request->get('start'), $request->get('finish'), $request->get('products'));
        return $this->respondSuccess([
            'report' => $response
        ]);
    }
}
