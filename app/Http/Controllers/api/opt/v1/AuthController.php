<?php

namespace App\Http\Controllers\api\opt\v1;

use App\Http\Controllers\api\BaseApiController;
use App\Http\Resources\Opt\User\AuthUserResource;
use App\Service\Opt\AuthService;
use App\v2\Models\WholesaleClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends BaseApiController
{
    public function register(Request $request, AuthService $authService): JsonResponse
    {
        if (WholesaleClient::whereEmail($request->get('email'))->exists()) {
            return $this->respondError('Пользователь с данным email уже зарегистрирован');
        }
        try {
            $user = $authService->register($request->all());
            return $this->respondSuccess(
                [
                    'user' => AuthUserResource::make($user)
                ],
                'Вы были успешно зарегистрированы',
                201
            );
        } catch (\Exception $exception) {
            return $this->respondError($exception->getMessage());
        }
    }

    public function me()
    {
        return $this->respondSuccessNoReport([
            'user' => AuthUserResource::make(auth()->user()),
        ]);
    }

    public function update(Request $request, AuthService $authService)
    {
        $authService->updateProfile($request->all(), auth()->user());
        return $this->respondSuccess([
            'user' => AuthUserResource::make(auth()->user()),
            'message' => 'Ваши данные успешно обновлены'
        ]);
    }

    public function login(Request $request, AuthService $authService): JsonResponse
    {
        $email = $request->get('email');
        $password = $request->get('password');
        try {
            $user = $authService->login($email, $password);
            \Auth::login($user);
            return $this->respondSuccess([
                'user' => AuthUserResource::make($user)
            ], 'Вы были успешно авторизованы');
        } catch (\Exception $exception) {
            return $this->respondError($exception->getMessage(),500,);
        }
    }

    /**
     * @throws \Exception
     */
    public function updatePassword(Request $request, AuthService $authService): JsonResponse
    {
        return $this->respondSuccess([
            'success' => $authService->updatePassword($request->all(), auth()->user()),
            'message' => 'Ваш пароль успешно обновлен'
        ]);
    }
}
