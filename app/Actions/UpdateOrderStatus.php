<?php

namespace App\Actions;

use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\User;
use App\OrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateOrderStatus
{
    public function __construct(private IssueReceipt $issueReceipt) {}

    public function handle(User $user, Order $order, OrderStatus $newStatus, ?string $reason = null): Order
    {
        return DB::transaction(function () use ($user, $order, $newStatus, $reason): Order {
            $order = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($order->business_id !== $user->business_id) {
                abort(404);
            }

            if ($newStatus === $order->status) {
                return $order->load(['items', 'receipt']);
            }

            $allowedStatuses = match ($order->status) {
                OrderStatus::Pending => [OrderStatus::Preparing, OrderStatus::Cancelled],
                OrderStatus::Preparing => [OrderStatus::Ready, OrderStatus::Cancelled],
                OrderStatus::Ready => [OrderStatus::Delivered, OrderStatus::Cancelled],
                default => [],
            };

            if (! in_array($newStatus, $allowedStatuses, true)) {
                throw ValidationException::withMessages([
                    'status' => 'El pedido no puede pasar a ese estado.',
                ]);
            }

            if ($newStatus === OrderStatus::Cancelled && $order->paid_at !== null) {
                throw ValidationException::withMessages([
                    'status' => 'No se puede cancelar un pedido cuyo pago ya fue confirmado.',
                ]);
            }

            $previousStatus = $order->status;
            $order->status = $newStatus;
            $order->delivered_at = $newStatus === OrderStatus::Delivered ? now() : $order->delivered_at;
            $order->cancellation_reason = $newStatus === OrderStatus::Cancelled ? $reason : null;

            if ($newStatus === OrderStatus::Delivered && $order->paid_at !== null) {
                $order->completed_at = now();
            }

            $order->save();

            OrderEvent::create([
                'business_id' => $order->business_id,
                'order_id' => $order->id,
                'user_id' => $user->id,
                'event_type' => 'status',
                'from_value' => $previousStatus->value,
                'to_value' => $newStatus->value,
                'note' => $newStatus === OrderStatus::Cancelled ? $reason : null,
                'occurred_at' => now(),
            ]);

            if ($order->isCompleted()) {
                $this->issueReceipt->handle($user, $order);
            }

            return $order->load(['items', 'receipt']);
        });
    }
}
