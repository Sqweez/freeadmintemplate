<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Concerns\ReturnsJsonResponse;
use App\Http\Controllers\Controller;
use App\User;

class BaseApiController extends Controller
{
    use ReturnsJsonResponse;

    protected ?User $user;

    public function __construct() {
        $this->user = $this->retrieveAnyUser();
    }

    public function retrieveAnyUser(): ?User
    {
        if (request()->has('logged_user_id')) {
            return User::find(\request()->get('logged_user_id', null));
        }
        /* @var User $user */
        $user = auth()->user();
        return $user;
    }
}
