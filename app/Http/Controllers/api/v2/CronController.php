<?php

namespace App\Http\Controllers\api\v2;

use App\Http\Controllers\api\CartController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\TelegramService;
use App\Order;
use App\v2\Models\OrderMessage;
use Illuminate\Http\Request;

class CronController extends Controller
{
    public function orderMessages() {
        return false;
        $messages = OrderMessage::where('is_delivered', false)->get();
        $messages->each(function ($message) {
            $order = Order::find($message['order_id']);
            if ($order['payment'] == Order::ORDER_PAYMENT_ONLINE && !$order['is_paid']) {
                return null;
            } else {
                if ($order !== null) {
                    $_message = (new CartController())->getMessage($order, null);
                    if (!(strlen($_message) > 4095)) {
                        \TelegramService::sendMessage($message['chat_id'], $_message);
                    }
                    OrderMessage::find($message['id'])->update([
                        'is_delivered' => true
                    ]);
                }
                else {
                    OrderMessage::find($message['id'])->delete();
                }
            }
        });
    }


    public function cancelOrders() {
        $orders = Order::whereDate('created_at', '<=', now()->subDays(2))
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->where('status', '==', 0)->get();
        $orders->each(function ($order) {
            (new OrderController())->decline($order);
        });
    }
}
