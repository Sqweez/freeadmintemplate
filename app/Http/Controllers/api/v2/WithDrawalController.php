<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\FileService;
use App\Http\Requests\WithDrawal\WithDrawalStoreRequest;
use App\Http\Resources\WithDrawal\WithDrawalListResource;
use App\v2\Models\WithDrawal;
use Illuminate\Http\Request;

class WithDrawalController extends Controller
{
    public function store(WithDrawalStoreRequest $request) {
        $data = $request->validated();
        $image = $request->file('image');
        $data['image'] = FileService::uploadData($image, 'withdrawals');
        $withdrawal = WithDrawal::create($data);
        return WithDrawalListResource::make($withdrawal);
    }

    public function index() {
        $user = auth()->user();
        $withDrawals = WithDrawal::query()
            ->when(!$user->is_super_user, function ($q) use ($user) {
                return $q->where('user_id', $user->id);
            })
            ->with(['user:id,name', 'store:id,name'])
            ->get();

        return WithDrawalListResource::collection($withDrawals);
    }

    public function destroy($id) {
        WithDrawal::query()->whereKey($id)->delete();
    }
}
