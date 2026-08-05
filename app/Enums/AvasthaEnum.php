<?php

namespace App\Enums;

enum AvasthaEnum: string
{
    case A = 'A';
    case N = 'N';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return array_map(fn($case) => ['label' => $case->name, 'value' => $case->value], self::cases());
    }
}