<?php

namespace App\Actions;

use App\Models\Business;
use App\Models\Order;
use App\Models\Receipt;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class IssueReceipt
{
    /**
     * Issue a sequential sales receipt for a completed order.
     */
    public function handle(User $user, Order $order): Receipt
    {
        if ($order->business_id !== $user->business_id) {
            throw ValidationException::withMessages([
                'order' => 'El pedido no pertenece a tu negocio.',
            ]);
        }

        return DB::transaction(function () use ($user, $order): Receipt {
            $order = Order::query()
                ->whereKey($order->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($order->business_id !== $user->business_id) {
                abort(404);
            }

            $existingReceipt = Receipt::query()
                ->where('business_id', $user->business_id)
                ->where('order_id', $order->id)
                ->first();

            if ($existingReceipt !== null) {
                return $existingReceipt;
            }

            if (! $order->isCompleted()) {
                throw ValidationException::withMessages([
                    'order' => 'El comprobante se emite cuando el pedido está entregado y pagado.',
                ]);
            }

            $business = Business::query()
                ->whereKey($user->business_id)
                ->lockForUpdate()
                ->firstOrFail();

            $business->increment('receipt_counter');
            $business->refresh();

            return Receipt::create([
                'business_id' => $business->id,
                'order_id' => $order->id,
                'number' => sprintf('C-%06d', $business->receipt_counter),
                'issued_at' => now(),
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'subtotal' => $order->subtotal,
                'tax' => $order->tax,
                'total' => $order->total,
            ]);
        });
    }
}
