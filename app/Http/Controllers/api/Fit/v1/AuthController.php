<?php

namespace App\Http\Controllers\api\Fit\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Fit\FitAuthUserResource;
use App\Models\FitUser;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $attributes = $request->only(['login', 'password']);
        $user = FitUser::query()
            ->with(['role', 'gym'])
            ->where('login', $attributes['login'])
            ->first();
        if ($user && \Hash::check($attributes['password'], $user->password)) {
            $user->update(['token' => \Str::random(30)]);
            $user->fresh();
            return response()->json([
                'status' => 'success',
                'user' => new FitAuthUserResource($user),
                'token' => $user->token,
            ], 200);
        } else {
            return response()->json(['message' => 'Неверные логин и пароль!'], 500);
        }
    }

    public function me(Request $request)
    {
        $token = $request->get('token');
        $user = FitUser::query()->whereToken($token)->first();
        if (!$user) {
            return response()->json(['error' => 'Неверный токен авторизации'], 500);
        }
        return response()->json([
            'status' => 'success',
            'user' => new FitAuthUserResource($user),
            'token' => $user->token,
        ], 200);
    }
}
