<?php

namespace App\Actions;

use App\Models\Business;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderEvent;
use App\Models\User;
use App\OrderOrigin;
use App\OrderStatus;
use App\Support\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateOrder
{
    /**
     * Register a new order and snapshot its product prices and costs.
     *
     * @param  array<string, mixed>  $data
     */
    public function handle(Business $business, array $data, ?User $actor, OrderOrigin $origin): Order
    {
        if ($actor !== null && $actor->business_id !== $business->id) {
            abort(404);
        }

        return DB::transaction(function () use ($business, $data, $actor, $origin): Order {
            $business = Business::query()
                ->whereKey($business->id)
                ->lockForUpdate()
                ->firstOrFail();

            $existingOrder = Order::query()
                ->where('business_id', $business->id)
                ->where('idempotency_key', $data['idempotency_key'])
                ->first();

            if ($existingOrder !== null) {
                return $existingOrder->load('items');
            }

            $selectedItems = array_filter(
                $data['items'],
                fn (array $line): bool => (int) $line['quantity'] > 0,
            );
            $itemIds = array_keys($selectedItems);

            if ($itemIds === []) {
                throw ValidationException::withMessages([
                    'items' => 'Agrega al menos un producto al pedido.',
                ]);
            }

            $menuItems = MenuItem::query()
                ->where('business_id', $business->id)
                ->whereIn('id', $itemIds)
                ->where('is_available', true)
                ->get()
                ->keyBy('id');

            if ($menuItems->count() !== count($itemIds)) {
                throw ValidationException::withMessages([
                    'items' => 'Uno o más productos ya no están disponibles.',
                ]);
            }

            $subtotalCents = 0;
            $costTotalCents = 0;
            $lines = [];

            foreach ($selectedItems as $itemId => $line) {
                $menuItem = $menuItems->get((int) $itemId);
                $quantity = (int) $line['quantity'];
                $subtotalCents += Money::toMinorUnits($menuItem->price) * $quantity;

                if ($menuItem->cost !== null) {
                    $costTotalCents += Money::toMinorUnits($menuItem->cost) * $quantity;
                }

                $lines[] = [
                    'business_id' => $business->id,
                    'menu_item_id' => $menuItem->id,
                    'name' => $menuItem->name,
                    'quantity' => $quantity,
                    'unit_price' => $menuItem->price,
                    'unit_cost' => $menuItem->cost,
                ];
            }

            $business->increment('order_counter');
            $business->refresh();

            $order = Order::create([
                'business_id' => $business->id,
                'order_number' => sprintf('PED-%06d', $business->order_counter),
                'idempotency_key' => $data['idempotency_key'],
                'public_token' => (string) Str::uuid(),
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'channel' => $data['channel'],
                'origin' => $origin,
                'status' => OrderStatus::Pending,
                'notes' => $data['notes'] ?? null,
                'delivery_address' => $data['delivery_address'] ?? null,
                'subtotal' => Money::fromMinorUnits($subtotalCents),
                'tax' => '0.00',
                'total' => Money::fromMinorUnits($subtotalCents),
                'cost_total' => Money::fromMinorUnits($costTotalCents),
                'profit' => Money::fromMinorUnits($subtotalCents - $costTotalCents),
                'created_by_user_id' => $actor?->id,
            ]);

            foreach ($lines as $line) {
                $order->items()->create($line);
            }

            OrderEvent::create([
                'business_id' => $business->id,
                'order_id' => $order->id,
                'user_id' => $actor?->id,
                'event_type' => 'status',
                'from_value' => null,
                'to_value' => OrderStatus::Pending->value,
                'occurred_at' => now(),
            ]);

            return $order->load('items');
        });
    }
}
