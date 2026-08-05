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
        Schema::create('ipd_anaesthesia_recover_observation', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ipd_id');
            $table->uuid('ipd_surgery_id');
            $table->uuid('ipd_anaesthesia_id');
            
            // SURGICAL DETAILS
            $table->text('surgical_procedure')->nullable();
            $table->dateTime('time_patient_received')->nullable();
            
            // POST OPERATIVE INSTRUCTIONS & MONITORS
            $table->text('post_operative_instructions')->nullable();
            
            // MONITORS (comma-separated: ECG, NIBP, SaO2, ABP, CVP, Urine Output, Pulse Rate, Blood Pressure, Respiration)
            $table->text('monitors')->nullable();
            
            // POST OPERATIVE COMPLICATIONS (comma-separated: Pain, Hypoxia, Nausea/Vomiting, Laryngospasm/Bronchospasm, Arrhythmias, Hypo/Hyperventilation, Hypo/Hypertension, Any Other)
            $table->text('post_operative_complications')->nullable();
            
            // POST OPERATIVE MEDICATIONS (text for medication list)
            $table->longText('post_operative_medications')->nullable();
            
            // RECOVERY SCORE
            $table->string('patient_score_on_admission')->nullable();
            $table->string('patient_score_before_transfer')->nullable();
            
            // VITALS MONITORING (JSON with observations: time, consciousness, resp, pulse, sao2, bp, remarks)
            $table->json('vital_monitoring')->nullable();
            
            // TRANSFER / DISCHARGE
            $table->text('transfer_to')->nullable();
            $table->dateTime('time_of_transfer')->nullable();
            $table->string('pulse_at_shifting')->nullable();
            $table->string('sbp_at_shifting')->nullable();
            $table->string('dbp_at_shifting')->nullable();
            $table->string('rr_at_shifting')->nullable();
            
            // SUMMARY
            $table->longText('summary')->nullable();
            
            // PDF UPLOAD
            $table->string('upload_pdf_path')->nullable();
            
            $table->timestamps();

            // Foreign key constraints (commented for flexibility)
            // $table->foreign('ipd_id')->references('id')->on('ipd')->onDelete('cascade');
            // $table->foreign('ipd_surgery_id')->references('id')->on('ipd_surgery')->onDelete('cascade');
            // $table->foreign('ipd_anaesthesia_id')->references('id')->on('ipd_anaesthesia')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ipd_anaesthesia_recover_observation');
    }
};
