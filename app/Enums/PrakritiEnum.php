<?php
namespace App\Enums;

enum PrakritiEnum: string {
    case PK = 'PK';
    case PV = 'PV';
    case KP = 'KP';
    case KV = 'KV';
    case VK = 'VK';
    case VP = 'VP';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return array_map(fn($case) => ['label' => $case->name, 'value' => $case->value], self::cases());
    }
}
