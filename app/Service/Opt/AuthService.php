<?php

namespace App\Service\Opt;

use App\v2\Models\City;
use App\v2\Models\WholesaleClient;

class AuthService
{
    public function register(array $payload): WholesaleClient
    {
        // @TODO edit it later
        $wholesaleUser = new WholesaleClient();
        $wholesaleUser->iin = $payload['IIN'];
        $wholesaleUser->first_name = $payload['firstName'];
        $wholesaleUser->last_name = $payload['lastName'];
        $wholesaleUser->patronymic = $payload['patronymic'];
        $wholesaleUser->country_id = $payload['countryId'];
        $wholesaleUser->city_id = City::firstOrCreate([
            'name' => $payload['city'],
            'country_id' => $payload['countryId']
        ])->id;
        $wholesaleUser->password = \Hash::make($payload['password']);
        $wholesaleUser->access_token = \Str::random(60);
        $wholesaleUser->phone = trim($payload['phone']);
        $wholesaleUser->email = $payload['email'];
        $wholesaleUser->save();
        return $wholesaleUser;
    }

    /**
     * @throws \Exception
     */
    public function login(string $email, string $password)
    {
        $user = WholesaleClient::whereEmail($email)->first();
        $errorMessage = 'Переданы некорретные данные';
        if (!$user) {
            throw new \Exception($errorMessage);
        }
        if (!\Hash::check($password, $user->password)) {
            throw new \Exception($errorMessage);
        }
        return $user;
    }

    public function updateProfile($payload, WholesaleClient $client): WholesaleClient
    {
        $client->iin = $payload['iin'];
        $client->first_name = $payload['first_name'];
        $client->last_name = $payload['last_name'];
        $client->patronymic = $payload['patronymic'];
        $client->has_russian_passport = $payload['has_russian_passport'];
        $client->legal_type_id = $payload['legal_type_id'];
        $client->passport = $payload['passport'];
        $client->delivery_address = $payload['address'];
        $client->city_id = City::firstOrCreate([
            'name' => $payload['city'],
            'country_id' => $client->country_id
        ])->id;
        $client->phone = trim($payload['phone']);
        $client->save();
        return $client;
    }
}
