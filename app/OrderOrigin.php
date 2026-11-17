<?php

namespace App;

enum OrderOrigin: string
{
    case Web = 'web';
    case Phone = 'phone';
    case Chat = 'chat';

    public function label(): string
    {
        return match ($this) {
            self::Web => 'Web',
            self::Phone => 'Teléfono',
            self::Chat => 'Chat',
        };
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function staffOptions(): array
    {
        return array_map(
            fn (self $origin): array => ['value' => $origin->value, 'label' => $origin->label()],
            [self::Phone, self::Chat],
        );
    }
}
