<?php

namespace App\Actions;

use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\User;
use App\OrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ConfirmOrderPayment
{
    public function __construct(private IssueReceipt $issueReceipt) {}

    public function handle(User $user, Order $order): Order
    {
        return DB::transaction(function () use ($user, $order): Order {
            $order = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($order->business_id !== $user->business_id) {
                abort(404);
            }

            if ($order->status === OrderStatus::Cancelled) {
                throw ValidationException::withMessages([
                    'payment' => 'No se puede confirmar el pago de un pedido cancelado.',
                ]);
            }

            if ($order->paid_at === null) {
                $order->paid_at = now();
                $order->paid_by_user_id = $user->id;

                if ($order->status === OrderStatus::Delivered) {
                    $order->completed_at = now();
                }

                $order->save();

                OrderEvent::create([
                    'business_id' => $order->business_id,
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'event_type' => 'payment',
                    'from_value' => 'unpaid',
                    'to_value' => 'paid',
                    'occurred_at' => now(),
                ]);
            }

            if ($order->isCompleted()) {
                $this->issueReceipt->handle($user, $order);
            }

            return $order->load(['items', 'receipt']);
        });
    }
}
