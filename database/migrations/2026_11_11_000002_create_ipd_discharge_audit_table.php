<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Additive + guarded. Audit trail of manual edits to ipd.discharge_date_time after discharge. */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ipd_discharge_audit')) {
            Schema::create('ipd_discharge_audit', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->char('ipd_id', 36)->index();
                $table->dateTime('old_discharge_date_time')->nullable();
                $table->dateTime('new_discharge_date_time');
                $table->unsignedBigInteger('changed_by')->nullable();
                $table->string('changed_by_name')->nullable();
                $table->string('changed_by_role')->nullable();
                $table->string('ip_address', 64)->nullable();
                $table->timestamp('created_at')->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ipd_discharge_audit');
    }
};
