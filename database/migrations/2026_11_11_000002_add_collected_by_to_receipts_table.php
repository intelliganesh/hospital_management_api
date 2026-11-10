<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Additive + guarded: records who collected a payment (used on the printed receipt).
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('receipts')) {
            return;
        }
        Schema::table('receipts', function (Blueprint $table) {
            if (! Schema::hasColumn('receipts', 'collected_by_user_id')) {
                $table->unsignedBigInteger('collected_by_user_id')->nullable();
            }
            if (! Schema::hasColumn('receipts', 'collected_by_name')) {
                $table->string('collected_by_name', 255)->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('receipts')) {
            return;
        }
        Schema::table('receipts', function (Blueprint $table) {
            foreach (['collected_by_user_id', 'collected_by_name'] as $col) {
                if (Schema::hasColumn('receipts', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
