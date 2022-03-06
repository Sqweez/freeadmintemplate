<?php

namespace App\Http\Controllers;

use App\Client;
use App\Http\Controllers\api\WaybillController;
use App\Http\Controllers\Services\TelegramService;
use App\Http\Resources\ClientResource;
use App\Http\Resources\v2\Product\ProductsResource;
use App\ProductBatch;
use App\Promocode;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use ProductService;

class CronController extends Controller
{
    public function disablePartners(Request $request) {
        $clients = Client::Partner()->whereDate('partner_expired_at', now())->pluck('id');
        Promocode::OfPartner($clients)->update(
            [
                'is_active' => false
            ]
        );

        Client::where('id', $clients)->update(
            [
                'is_partner' => false
            ]
        );
    }

    public function revokeSellerToken() {
        $users = User::query()
            ->where('role_id', 2)
            ->orWhere('id', 29)
            ->get();
        return $users->each(function (User $user) {
            return $user->update(['token' => Str::random(60)]);
        });
    }

    public function getPlatinumClientsRemainder(TelegramService $telegramService) {
        $clients = ClientResource::collection(
            Client::with(['sales', 'transactions', 'city', 'loyalty'])
                ->platinumClients()
                ->get()
        )->toArray(\request());
        $clients = collect($clients)->filter(function ($client) { return $client['until_platinum'] > 0; })->values();
        $message = ($this->getPlatinumRemainderMessage($clients));
        $chatId = config('telegram.BIRTHDAY_CHAT');
        $telegramService->sendMessage($chatId, $message);
        return response([], 200);
    }

    public function getBirthdayClients(TelegramService $telegramService) {
        $clientsWithBirthday = Client::query()
            ->whereNotNull('birth_date')
            ->whereMonth('birth_date', now()->month)
            ->whereDay('birth_date', now()->day)
            ->with('city')
            ->get();


        if (!$clientsWithBirthday || $clientsWithBirthday->count() === 0) {
            $message = "Сегодня нет дней рождения";
        } else {
            $message = $this->getBirthDayMessage($clientsWithBirthday);
        }
        $chatId = config('telegram.BIRTHDAY_CHAT');
        $telegramService->sendMessage($chatId, $message);
        return response([], 200);
    }

    private function getBirthDayMessage($clients) {
        $message = "Сегодня день рождения у 🎂🥳🎁🎉" . "\n";
        collect($clients)->each(function ($client, $key) use (&$message){
            $message .= sprintf("%s. %s", ($key + 1), $client->client_name);
            $message .= "\n";
            $message .= "Телефон: " . $client->client_phone . "\n";
            $message .= "Город: " . $client->city->name . "\n";
        });
        return urlencode($message);
    }

    private function getPlatinumRemainderMessage($clients) {
        $message = "Платиновые клиенты, у которых не хватает покупок:" . "\n";
        collect($clients)->each(function ($client, $key) use (&$message){
            $message .= sprintf("%s. %s", ($key + 1), $client['client_name']);
            $message .= "\n";
            $message .= "Телефон: " . $client['client_phone'] . "\n";
            $message .= "Город: " . $client['city'] . "\n";
            $message .= "Остаток:" . $client['until_platinum'] . "₸" . "\n";
            $message .= "<a href='https://api.whatsapp.com/send?phone=" . $client['client_phone'] . "'>Открыть Whatsapp</a>" . "\n";
        });
        return urlencode($message);
    }

    public function storePriceList(Request $request) {
        $products = ProductsResource::collection(ProductService::all())->toArray($request);
        $batches = ProductBatch::query()
            ->whereIn('store_id', [1, 6])
            ->where('quantity', '>', 0)
            ->select(['quantity', 'product_id'])
            ->get();

        $quantities = $batches
            ->groupBy('product_id')
            ->map(function ($item, $key) {
                return [
                    'product_id' => $key,
                    'quantity' => collect($item)->reduce(function ($a, $c) {
                        return $a + $c['quantity'];
                    }, 0)
                ];
            })->values();

        $products = collect($products)->map(function ($product) use ($quantities) {
            $id = $product['id'];
            $qnt = collect($quantities)->filter(function ($q) use ($id) {
                return $q['product_id'] == $id;
            })->reduce(function ($a, $c) {
                return $a + $c['quantity'];
            }, 0);
            unset($product['quantity']);
            $product['quantity'] = $qnt;
            return $product;
        })->values()->filter(function ($i) {
            return $i['quantity'] > 0;
        })->values()->sortBy('manufacturer_id')->values();


        return (new WaybillController())->getPriceList(new Request(), $products);
    }
}
