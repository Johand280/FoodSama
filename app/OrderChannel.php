<?php

namespace App;

enum OrderChannel: string
{
    case DineIn = 'dine_in';
    case Takeaway = 'takeaway';
    case Delivery = 'delivery';

    public function label(): string
    {
        return match ($this) {
            self::DineIn => 'En local',
            self::Takeaway => 'Para llevar',
            self::Delivery => 'Delivery',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::DineIn => 'utensils',
            self::Takeaway => 'bag',
            self::Delivery => 'truck',
        };
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $channel): array => ['value' => $channel->value, 'label' => $channel->label()],
            self::cases(),
        );
    }
}
