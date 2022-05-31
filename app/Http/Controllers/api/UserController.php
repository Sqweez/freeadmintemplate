<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\User;
use App\UserRole;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        return UserResource::collection(User::with(['store', 'role'])->get());
    }

    public function indexRoles() {
        return UserRole::all();
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return UserResource
     */
    public function store(Request $request)
    {
        $user = $request->all();
        $user['token'] = Str::random(60);
        $_user = User::create($user);
        return new UserResource($_user);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return UserResource
     */
    public function update(Request $request, User $user)
    {
        $user->update($request->all());
        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return void
     * @throws \Exception
     */
    public function destroy(User $user)
    {
        $user->delete();
    }

    public function auth(Request $request) {
        $token = $request->get('token');
        $user = User::Token($token)->first();
        if (!$user) {
            return response()->json(['error' => 'Неверный токен авторизации'], 500);
        }
        return response()->json([
            'status' => 'success',
            'user' => new UserResource($user),
            'token' => $user->token,
        ], 200);
    }

    public function login(Request $request) {
        $attributes = $request->only(['login', 'password']);
        if (Auth::attempt($attributes)) {
            $user = User::Login($attributes['login'])->first();
            return response()->json([
                'status' => 'success',
                'user' => new UserResource($user),
                'token' => $user->token,
            ], 200);
        } else {
            return response()->json(['message' => 'Неверные логин и пароль!'], 500);
        }
    }
}
