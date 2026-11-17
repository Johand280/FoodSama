<?php

namespace App\Models;

use App\Models\Concerns\BelongsToBusiness;
use App\OrderChannel;
use App\OrderOrigin;
use App\OrderStatus;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'business_id',
    'order_number',
    'customer_name',
    'customer_phone',
    'channel',
    'origin',
    'status',
    'notes',
    'delivery_address',
    'subtotal',
    'tax',
    'total',
    'cost_total',
    'profit',
    'completed_at',
    'delivered_at',
    'paid_at',
    'cancellation_reason',
    'public_token',
    'idempotency_key',
    'created_by_user_id',
    'paid_by_user_id',
])]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use BelongsToBusiness, HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'channel' => OrderChannel::class,
            'origin' => OrderOrigin::class,
            'status' => OrderStatus::class,
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'cost_total' => 'decimal:2',
            'profit' => 'decimal:2',
            'completed_at' => 'datetime',
            'delivered_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * Get the lines belonging to the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the receipt issued for the order.
     */
    public function receipt(): HasOne
    {
        return $this->hasOne(Receipt::class);
    }

    /**
     * Get the recorded state and payment changes.
     */
    public function events(): HasMany
    {
        return $this->hasMany(OrderEvent::class)->orderBy('occurred_at');
    }

    /**
     * Get the user who created this order.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Get the user who confirmed payment.
     */
    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by_user_id');
    }

    /**
     * Determine whether the order has been delivered and paid.
     */
    public function isCompleted(): bool
    {
        return $this->status === OrderStatus::Completed
            || ($this->status === OrderStatus::Delivered && $this->paid_at !== null);
    }
}
