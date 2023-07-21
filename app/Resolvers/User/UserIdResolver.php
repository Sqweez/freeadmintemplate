<?php

namespace App\Resolvers\User;

class UserIdResolver
{
    public static function resolve($key = 'user_id')
    {
        return auth()->id() ?: request($key);
    }
}
