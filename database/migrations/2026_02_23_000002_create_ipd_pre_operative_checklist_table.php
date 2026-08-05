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
        Schema::create('ipd_pre_operative_checklist', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ipd_id');
            $table->uuid('ipd_surgery_id')->unique();
            $table->text('q01_investigations')->nullable();
            $table->text('q02_chest_xray_ecg')->nullable();
            $table->text('q03_minor_age_parents')->nullable();
            $table->text('q04a_blood_thinners')->nullable();
            $table->text('q04b_blood_thinners_details')->nullable();
            $table->text('q05a_asthma')->nullable();
            $table->text('q05b_asthma_treatment')->nullable();
            $table->text('q06_medication_allergy')->nullable();
            $table->text('q07_tooth_extraction')->nullable();
            $table->text('q08_surgical_procedure')->nullable();
            $table->text('q09a_diabetic')->nullable();
            $table->text('q09b_blood_sugar')->nullable();
            $table->text('q10_thyroid_medication')->nullable();
            $table->text('q11a_hypertension')->nullable();
            $table->text('q11b_hypertension_medicine')->nullable();
            $table->text('q11c_hypertension_medication_taken')->nullable();
            $table->text('q12_informed_consent')->nullable();
            $table->text('q13_anesthesia_awareness')->nullable();
            $table->text('q14_operative_procedure_awareness')->nullable();
            $table->text('q15a_male_patient_age')->nullable();
            $table->text('q15b_urinary_symptoms')->nullable();
            $table->text('q16_urinary_obstruction')->nullable();
            $table->text('q17_lithotomy_position')->nullable();
            $table->text('q18_previous_surgery')->nullable();
            $table->text('q19_community')->nullable();
            $table->text('q20_previous_surgery_events')->nullable();
            $table->text('q21_female_pregnant')->nullable();
            $table->text('q22_epilepsy')->nullable();
            $table->text('q23_antipsychotic')->nullable();
            $table->text('q24_last_food_intake')->nullable();
            $table->text('summary')->nullable();
            $table->dateTime('datetime')->nullable();
            $table->string('upload_pdf_path')->nullable();
            $table->timestamps();

            // Foreign key constraint (commented for flexibility)
            // $table->foreign('ipd_id')->references('id')->on('ipd')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ipd_pre_operative_checklist');
    }
};
