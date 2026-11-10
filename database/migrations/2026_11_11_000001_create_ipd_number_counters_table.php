<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Additive + guarded. One row per IPD number series (e.g. "ash-ayu-", "ash-all-").
 * last_number only ever increases, so numbers are never reused after an IPD is deleted.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ipd_number_counters')) {
            Schema::create('ipd_number_counters', function (Blueprint $table) {
                $table->string('prefix', 50)->primary();
                $table->unsignedBigInteger('last_number')->default(0);
                $table->timestamps();
            });
        }

        // Pre-create both series rows so concurrent first admissions never race on INSERT.
        $base = strtolower(trim((string) config('services.hospital_app.ipd_code_prefix', 'ash'))) ?: 'ash';
        foreach (['ayu', 'all'] as $series) {
            DB::table('ipd_number_counters')->insertOrIgnore([
                'prefix' => "{$base}-{$series}-", 'last_number' => 0, 'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ipd_number_counters');
    }
};
