<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Shloka master + shloka snapshot on the surgery report.
 * Additive and guarded; safe to run on a database where either already exists.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('shloka')) {
            Schema::create('shloka', function (Blueprint $table) {
                $table->id();
                $table->string('title', 255);
                $table->text('verse_text');
                $table->enum('status', ['Active', 'Inactive'])->default('Active');
                $table->timestamps();
            });
        }

        if (Schema::hasTable('ipd_surgery') && ! Schema::hasColumn('ipd_surgery', 'shloka_data')) {
            Schema::table('ipd_surgery', function (Blueprint $table) {
                // JSON array of {id, title, text} snapshots chosen on the surgery report
                $table->longText('shloka_data')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ipd_surgery') && Schema::hasColumn('ipd_surgery', 'shloka_data')) {
            Schema::table('ipd_surgery', function (Blueprint $table) {
                $table->dropColumn('shloka_data');
            });
        }
        Schema::dropIfExists('shloka');
    }
};
