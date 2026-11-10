<?php

namespace App\Support;

/**
 * Single source of truth for clinical measurement units shown in PDFs, reports and Excel exports.
 * Keep in sync with frontend/src/utils/measurementUnits.ts.
 */
class MeasurementUnits
{
    /** field key (as stored in examinations / consultations / IPD notes) => standard unit */
    public const UNITS = [
        'bp'                 => 'mmHg',
        'blood_pressure'     => 'mmHg',
        'pulse'              => 'bpm',
        'pulse_rate'         => 'bpm',
        'heart_rate'         => 'bpm',
        'spo2'               => '%',
        'sp_o2'              => '%',
        'temperature'        => '°F',
        'temp'               => '°F',
        'respiratory_rate'   => '/min',
        'rr'                 => '/min',
        'weight'             => 'kg',
        'height'             => 'cm',
        'blood_sugar'        => 'mg/dL',
        'rbs'                => 'mg/dL',
        'fbs'                => 'mg/dL',
        'ppbs'               => 'mg/dL',
        'hemoglobin'         => 'g/dL',
        'haemoglobin'        => 'g/dL',
        'hb'                 => 'g/dL',
    ];

    public static function unit(string $key): ?string
    {
        return self::UNITS[strtolower(trim($key))] ?? null;
    }

    /**
     * Append the standard unit to a value unless it is empty or already carries a unit
     * (e.g. "98.6 °F", "120/80 mmHg", "98 %"). Temperature already stored with its own unit (°C/°F) is kept as is.
     */
    public static function format(string $key, $value): string
    {
        $value = is_null($value) ? '' : trim((string) $value);
        if ($value === '') {
            return '';
        }
        $unit = self::unit($key);
        if (! $unit || preg_match('/[A-Za-z%°]/', $value)) {
            return $value;
        }
        return $value . ' ' . $unit;
    }

    /** "Pulse (bpm)" style label */
    public static function label(string $label, string $key): string
    {
        $unit = self::unit($key);
        return $unit ? "{$label} ({$unit})" : $label;
    }
}
