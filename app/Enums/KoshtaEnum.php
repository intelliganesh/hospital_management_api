<?php

namespace App\Enums;

enum KoshtaEnum: string
{
    case K = 'K';
    case M = 'M';
    case MD = 'MD';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return array_map(fn($case) => ['label' => $case->name, 'value' => $case->value], self::cases());
    }
}