<?php

namespace App\Http\Controllers\api\Fit\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Fit\FitUsersListResource;
use App\Models\FitUser;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        /* @var FitUser $user */
        $user = auth()->user();
        $users = FitUser::query()
            ->where('gym_id', $user->gym_id)
            ->with(['role', 'gym'])
            ->get();

        return FitUsersListResource::collection($users);
    }

    public function store(Request $request): FitUsersListResource
    {
        $data = $request->all();
        $data['fit_role_id'] = $request->get('role_id');
        $data['gym_id'] = auth()->user()->gym_id;
        $user = FitUser::create($data);
        return new FitUsersListResource($user);
    }

    public function update(FitUser $user, Request $request): FitUsersListResource
    {
        $data = $request->only(['name', 'login']);
        $data['fit_role_id'] = $request->get('role_id');
        if ($request->filled('password')) {
            $data['password'] = $request->get('password');
            $user->update([
                'token' => null
            ]);
        }

        $user->update($data);

        return new FitUsersListResource($user);
    }
}
