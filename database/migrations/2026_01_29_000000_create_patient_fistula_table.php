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
        Schema::create('patient_fistula', function (Blueprint $table) {
            $table->id();
            $table->uuid('patient_id');
            $table->string('patient_email')->nullable();
            $table->string('patient_phone')->nullable();
            $table->string('patient_name')->nullable();
            $table->string('patient_number')->nullable();
            
            $table->string('no_of_fistula')->nullable();
            $table->text('no_of_tracks_in_one_fistula')->nullable();
            
            $table->text('no_of_external_opening_position')->nullable();
            $table->text('external_opening_position')->nullable();
            
            $table->text('internal_opening_position')->nullable();
            $table->text('internal_opening_distance')->nullable();
            $table->text('any_other')->nullable();
            
            $table->text('no_of_secondary_opening_position')->nullable();
            $table->text('secondary_opening_position')->nullable();
            $table->text('secondary_anal_valve')->nullable();
            
            $table->text('other_investigation')->nullable();
            $table->text('anal_valve')->nullable();
            
            $table->text('type_of_crypt')->nullable();
            $table->text('crypt_cause')->nullable();
            
            $table->text('type_of_fistula_position')->nullable();
            $table->text('type_of_fistula_sphincter')->nullable();
            $table->text('basis_of_high_low_riding')->nullable();
            $table->text('distant_visceral_communication')->nullable();
            
            $table->string('sono_fistula_gram')->nullable();
            $table->string('mri_fistula_gram')->nullable();
            
            $table->text('sonologist_findings')->nullable();
            $table->string('fistula_recurrence')->nullable();
            $table->string('fistula_recurrence_surgery_count')->nullable();
            $table->text('fistula_remark')->nullable();
            $table->string('posterior_fistulous_angle')->nullable();
            $table->string('sonologist')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            
            $table->timestamps();
            
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_fistula');
    }
};
