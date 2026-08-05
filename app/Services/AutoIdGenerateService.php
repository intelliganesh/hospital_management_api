<?php
namespace App\Services;

use App\Enums\ServiceType;
use App\Models\SystemSettings;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

}
