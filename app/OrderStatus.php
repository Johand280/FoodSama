<?php

namespace App;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Preparing = 'preparing';
    case Ready = 'ready';
    case Delivered = 'delivered';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendiente',
            self::Preparing => 'En preparación',
            self::Ready => 'Listo',
            self::Delivered => 'Entregado',
            self::Completed => 'Completado',
            self::Cancelled => 'Cancelado',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-100 text-amber-800 ring-amber-600/20',
            self::Preparing => 'bg-blue-100 text-blue-800 ring-blue-600/20',
            self::Ready => 'bg-violet-100 text-violet-800 ring-violet-600/20',
            self::Delivered => 'bg-emerald-100 text-emerald-800 ring-emerald-600/20',
            self::Completed => 'bg-emerald-100 text-emerald-800 ring-emerald-600/20',
            self::Cancelled => 'bg-rose-100 text-rose-800 ring-rose-600/20',
        };
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $status): array => ['value' => $status->value, 'label' => $status->label()],
            array_values(array_filter(self::cases(), fn (self $status): bool => $status !== self::Completed)),
        );
    }
}
