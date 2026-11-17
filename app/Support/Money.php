<?php

namespace App\Support;

class Money
{
    public static function toMinorUnits(int|float|string|null $amount): int
    {
        $amount = trim((string) ($amount ?? '0'));

        if (! preg_match('/^(-?)(\d+)(?:\.(\d{1,2}))?$/', $amount, $matches)) {
            throw new \InvalidArgumentException('El valor monetario debe tener máximo dos decimales.');
        }

        $minorUnits = ((int) $matches[2] * 100) + (int) str_pad($matches[3] ?? '', 2, '0');

        return ($matches[1] ?? '') === '-' ? -$minorUnits : $minorUnits;
    }

    public static function fromMinorUnits(int $amount): string
    {
        $sign = $amount < 0 ? '-' : '';
        $absoluteAmount = abs($amount);

        return $sign.intdiv($absoluteAmount, 100).'.'.str_pad((string) ($absoluteAmount % 100), 2, '0', STR_PAD_LEFT);
    }
}
