<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * High/Low riding is now chosen per fistula ('#'-joined like the other fistula
 * fields), so proctology.high_low_riding_type (varchar(20)) is widened to TEXT
 * and the column is added to patient_fistula (the per-patient carry-forward row).
 * Additive / guarded; existing values are untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('proctology') && Schema::hasColumn('proctology', 'high_low_riding_type')) {
            DB::statement('ALTER TABLE `proctology` MODIFY `high_low_riding_type` TEXT NULL');
        }

        if (Schema::hasTable('patient_fistula') && ! Schema::hasColumn('patient_fistula', 'high_low_riding_type')) {
            Schema::table('patient_fistula', function (Blueprint $table) {
                $table->text('high_low_riding_type')->nullable()->after('type_of_fistula_sphincter');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('patient_fistula') && Schema::hasColumn('patient_fistula', 'high_low_riding_type')) {
            Schema::table('patient_fistula', function (Blueprint $table) {
                $table->dropColumn('high_low_riding_type');
            });
        }
        // proctology.high_low_riding_type is intentionally left as TEXT on rollback (narrowing could truncate data).
    }
};
