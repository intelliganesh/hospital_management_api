<?php
namespace App\Services;

use App\Enums\ServiceType;
use App\Models\SystemSettings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AutoIdGenerateService
{
    /**
     * Generate a unique ID for the given service type
     *
     * @param ServiceType $type The service type to generate an ID for
     * @return string The generated ID with prefix and number
     * @throws \Exception If system settings are not found or prefix is missing
     */
    public function generateId(ServiceType $type): string
    {
        $user           = Auth::user();
        $model          = $type->model();
        $fieldName      = $type->value . "_number";
        $statusKey      = $type->value . "_status";
        $startNumberKey = $type->value . "_start_number";
        $prefixKey      = $type->value . "_prefix";

        // Get system settings once to avoid multiple database queries
        $systemSettings = SystemSettings::where('id', $user->system_settings_id)->first();
        if (empty($systemSettings)) {
            throw new \Exception("System settings not found for user");
        }

        // Default to 0 if no previous number is found
        $lastNumber = 0;

        // Use database transaction to ensure number consistency
        // Use database transaction to ensure number consistency
        return DB::transaction(function () use ($type, $model, $fieldName, $statusKey, $startNumberKey, $prefixKey, $systemSettings) {
            // Default to 0 if no previous number is found
            $lastNumber = 0;

            // Skip custom numbering for hospital type
            if ($type->value === 'hospital') {
                $lastRow = $model::whereNotNull($fieldName)
                    ->orderBy($fieldName, 'desc')
                    ->lockForUpdate()
                    ->first();

                if ($lastRow && ! empty($lastRow->$fieldName)) {
                    // Extract numeric part from the ID
                    if (preg_match('/([A-Za-z]*)([0-9]+)$/', $lastRow->$fieldName, $matches)) {
                        $lastNumber = isset($matches[2]) ? intval($matches[2]) : 0;
                    }
                }
            } else {
                // Check if custom numbering is enabled for this type and the property exists
                $hasCustomNumbering = isset($systemSettings->$statusKey) && $systemSettings->$statusKey;
                $hasStartNumber     = isset($systemSettings->$startNumberKey) && ! is_null($systemSettings->$startNumberKey);

                if ($hasCustomNumbering && $hasStartNumber) {
                    // When custom numbering is enabled and start number is set,
                    // always use the start number from system settings
                    // $startNumber = intval($systemSettings->$startNumberKey);

                    // // Check if there are existing records
                    // $lastRow = $model::whereNotNull($fieldName)
                    //     ->orderBy($fieldName, 'desc')
                    //     ->lockForUpdate()
                    //     ->first();

                    // if ($lastRow && ! empty($lastRow->$fieldName)) {
                    //     // Extract numeric part from the ID
                    //     if (preg_match('/([A-Za-z]*)([0-9]+)$/', $lastRow->$fieldName, $matches)) {
                    //         $existingNumber = isset($matches[2]) ? intval($matches[2]) : 0;
                    //         // Use the higher of the existing number or the start number
                    //         $lastNumber = max($existingNumber, $startNumber - 1);
                    //     }
                    // } else {
                    //     // No records exist, use the start number from system settings
                    //     $lastNumber = $startNumber - 1;
                    // }


                    $startNumber = intval($systemSettings->$startNumberKey);

                    $lastRow = $model::whereNotNull($fieldName)
                        ->orderByRaw(
                            "CAST(REGEXP_REPLACE($fieldName, '[^0-9]', '') AS UNSIGNED) DESC"
                        )
                        ->lockForUpdate()
                        ->first();

                    // Log::info("AutoIdGenerate Query Debug", [
                    //     'model' => $model,
                    //     'fieldName' => $fieldName,
                    //     'allRecords' => $model::whereNotNull($fieldName)->orderByRaw("CAST(REGEXP_REPLACE($fieldName, '[^0-9]', '') AS UNSIGNED) DESC")->limit(5)->pluck($fieldName)->toArray(),
                    // ]);

                    if ($lastRow && ! empty($lastRow->$fieldName)) {
                        // Extract the numeric part from the ID (handles both prefixed and non-prefixed)
                        if (preg_match('/([0-9]+)$/', $lastRow->$fieldName, $matches)) {
                            $existingNumber = intval($matches[1]);
                            // Always use the existing number, it's already the highest
                            $lastNumber = $existingNumber;
                        } else {
                            $lastNumber = $startNumber - 1;
                        }
                    } else {
                        $lastNumber = $startNumber - 1;
                    }


                } else {
                    // Use standard numbering - find the highest number by querying the database
                    $lastRow = $model::whereNotNull($fieldName)
                        ->orderByRaw(
                            "CAST(REGEXP_REPLACE($fieldName, '[^0-9]', '') AS UNSIGNED) DESC"
                        )
                        ->lockForUpdate()
                        ->first();

                    if ($lastRow && ! empty($lastRow->$fieldName)) {
                        // Extract numeric part from the ID, improved regex to handle various formats
                        if (preg_match('/([0-9]+)$/', $lastRow->$fieldName, $matches)) {
                            $lastNumber = isset($matches[1]) ? intval($matches[1]) : 0;
                        }
                    }
                }
            }

            // Generate the new number with padding
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

            // Get the prefix from system settings
            if (! isset($systemSettings->$prefixKey) || empty($systemSettings->$prefixKey)) {
                throw new \Exception("Prefix not found for {$type->value}");
            }

            $prefix = $systemSettings->$prefixKey;
            $generatedId = $prefix . $newNumber;
            
            // Debug logging
            // Log::info("AutoIdGenerate Debug", [
            //     'type' => $type->value,
            //     'lastRow' => $lastRow?->$fieldName ?? 'null',
            //     'lastNumber' => $lastNumber,
            //     'newNumber' => $newNumber,
            //     'prefix' => $prefix,
            //     'generatedId' => $generatedId,
            //     'startNumber' => $startNumber ?? 'not set',
            // ]);
            
            return $generatedId;
        });
    }

    /**
     * Next admission number for a NEW IPD: "<prefix>-ayu-NN" (Ayurveda) or "<prefix>-all-NN" (Allopathy),
     * lowercase, NN zero-padded to 2 digits and growing naturally (99 -> 100).
     * Legacy numbers (IPD0001...) are never touched.
     *
     * Race safety: one row per series in ipd_number_counters is locked (FOR UPDATE) until the caller's
     * transaction ends, so IpdService::create() wraps the whole admission in DB::transaction -> a failed
     * admission rolls the counter back too (no gaps) and concurrent admissions are serialised per series.
     * The counter never decreases, so deleted numbers are not reused. Final guard: UNIQUE(ipd.ipd_number).
     * If the counter table is missing (migration not run) it falls back to max(existing suffix)+1.
     */
    public function generateIpdNumber(string $treatmentType = 'ayurveda'): string
    {
        $series = strtolower($treatmentType) === 'allopathy' ? 'all' : 'ayu';
        $base   = strtolower(trim((string) config('services.hospital_app.ipd_code_prefix', 'ash')));
        $prefix = ($base !== '' ? $base : 'ash') . '-' . $series . '-';

        return DB::transaction(function () use ($prefix) {
            $existingMax = (int) DB::table('ipd')
                ->whereRaw('ipd_number REGEXP ?', ['^' . preg_quote($prefix, '/') . '[0-9]+$'])
                ->selectRaw('MAX(CAST(SUBSTRING(ipd_number, ?) AS UNSIGNED)) AS max_no', [strlen($prefix) + 1])
                ->value('max_no');

            if (Schema::hasTable('ipd_number_counters')) {
                $row = DB::table('ipd_number_counters')->where('prefix', $prefix)->lockForUpdate()->first();
                if (! $row) {
                    DB::table('ipd_number_counters')->insertOrIgnore([
                        'prefix' => $prefix, 'last_number' => 0, 'created_at' => now(), 'updated_at' => now(),
                    ]);
                    $row = DB::table('ipd_number_counters')->where('prefix', $prefix)->lockForUpdate()->first();
                }
                $next = max((int) $row->last_number, $existingMax) + 1;
                DB::table('ipd_number_counters')->where('prefix', $prefix)
                    ->update(['last_number' => $next, 'updated_at' => now()]);
            } else {
                DB::table('ipd')->where('ipd_number', 'like', $prefix . '%')->lockForUpdate()->pluck('id');
                $next = $existingMax + 1;
            }

            return $prefix . str_pad((string) $next, 2, '0', STR_PAD_LEFT);
        });
    }

}
