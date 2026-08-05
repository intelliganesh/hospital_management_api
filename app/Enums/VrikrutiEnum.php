<?php
namespace App\Enums;

enum VrikrutiEnum: string {
    case K_INCREASE = 'K ↑';
    case K_DECREASE = 'K ↓';
    case K_BALANCED = 'K -';

    case P_INCREASE = 'P ↑';
    case P_DECREASE = 'P ↓';
    case P_BALANCED = 'P -';

    case V_INCREASE = 'V ↑';
    case V_DECREASE = 'V ↓';
    case V_BALANCED = 'V -';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return array_map(fn($case) => ['label' => $case->name, 'value' => $case->value], self::cases());
    }
}
