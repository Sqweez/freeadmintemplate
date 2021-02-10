<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\api\CartController;
use App\Http\Controllers\Controller;
use App\Order;
use App\v2\Models\OrderMessage;
use Illuminate\Http\Request;

class CronController extends Controller
{
    public function orderMessages() {
        $messages = OrderMessage::where('is_delivered', false)->get();
        $messages->each(function ($message) {
            try {
                $order = Order::find($message['order_id']);
                $_message = (new CartController())->getMessage($order, null);
                \TelegramService::sendMessage($message['chat_id'], $_message);
                OrderMessage::find($message['id'])->update([
                    'is_delivered' => true
                ]);
            } catch (\Exception $exception) {
                echo $exception->getMessage();
            }
        });
    }

    public function cancelOrders() {
        $orders = Order::whereDate('created_at', '<=', now()->subDays(2))
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->where('status', '==', 0)->get();
        $orders->each(function ($order) {
            (new CartController())->decline($order);
        });
    }
}
