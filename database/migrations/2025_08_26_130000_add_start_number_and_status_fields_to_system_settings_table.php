<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            
            // OPD fields
            $table->integer('opd_start_number')->nullable()->after('opd_prefix');
            $table->boolean('opd_status')->nullable()->after('opd_start_number');
            
            // Findings fields
            $table->integer('findings_start_number')->nullable()->after('findings_prefix');
            $table->boolean('findings_status')->nullable()->after('findings_start_number');
            
            // IPD fields
            $table->integer('ipd_start_number')->nullable()->after('ipd_prefix');
            $table->boolean('ipd_status')->nullable()->after('ipd_start_number');
            
            // Patient fields
            $table->integer('patient_start_number')->nullable()->after('patient_prefix');
            $table->boolean('patient_status')->nullable()->after('patient_start_number');
            
            // Appointment fields
            $table->integer('appointment_start_number')->nullable()->after('appointment_prefix');
            $table->boolean('appointment_status')->nullable()->after('appointment_start_number');
            
            // Payment fields
            $table->integer('payment_start_number')->nullable()->after('payment_prefix');
            $table->boolean('payment_status')->nullable()->after('payment_start_number');
            
            // Test fields
            $table->integer('test_start_number')->nullable()->after('test_prefix');
            $table->boolean('test_status')->nullable()->after('test_start_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            
            // OPD fields
            $table->dropColumn('opd_start_number');
            $table->dropColumn('opd_status');
            
            // Findings fields
            $table->dropColumn('findings_start_number');
            $table->dropColumn('findings_status');
            
            // IPD fields
            $table->dropColumn('ipd_start_number');
            $table->dropColumn('ipd_status');
            
            // Patient fields
            $table->dropColumn('patient_start_number');
            $table->dropColumn('patient_status');
            
            // Appointment fields
            $table->dropColumn('appointment_start_number');
            $table->dropColumn('appointment_status');
            
            // Payment fields
            $table->dropColumn('payment_start_number');
            $table->dropColumn('payment_status');
            
            // Test fields
            $table->dropColumn('test_start_number');
            $table->dropColumn('test_status');
        });
    }
};
