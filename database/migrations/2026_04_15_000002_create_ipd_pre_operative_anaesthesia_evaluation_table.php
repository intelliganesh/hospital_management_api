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
        Schema::create('ipd_pre_operative_anaesthesia_evaluation', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ipd_id');
            $table->uuid('ipd_surgery_id')->unique();
            $table->uuid('ipd_anaesthesia_id');
            $table->text('previous_anaesthesia_surgery')->nullable();
            $table->text('current_medication')->nullable();
            $table->text('allergies')->nullable();
            $table->string('asa_grading')->nullable();
            $table->text('airway_assessment')->nullable();
            $table->text('respiratory_system')->nullable();
            $table->text('cardio_vascular_system')->nullable();
            $table->text('cns_musculoskeletal')->nullable();
            $table->text('hepatic_renal')->nullable();
            $table->text('endocrine')->nullable();
            $table->text('other_system')->nullable();
            $table->text('clinical_evaluation')->nullable();
            $table->string('hb_hct')->nullable();
            $table->string('tc')->nullable();
            $table->string('platelets')->nullable();
            $table->string('bt_ct')->nullable();
            $table->string('pt_ptt')->nullable();
            $table->string('inr')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('fbs_rbs')->nullable();
            $table->string('bun')->nullable();
            $table->string('na_k')->nullable();
            $table->text('chest_xray')->nullable();
            $table->text('ecg')->nullable();
            $table->text('echo')->nullable();
            $table->text('other_investigation')->nullable();
            $table->text('specific_anaesthesia_problem')->nullable();
            $table->text('pre_operative_anaesthesia_instruction')->nullable();
            $table->text('summary')->nullable();
            $table->dateTime('datetime')->nullable();
            $table->string('upload_pdf_path')->nullable();
            $table->string('mouth_opening')->nullable();
            $table->string('teeth')->nullable();
            $table->string('neck_movement')->nullable();
            $table->string('mallampati_score')->nullable();
            $table->string('dentures_check')->nullable();
            $table->string('tmd')->nullable();
            $table->timestamps();

            // Foreign key constraint (commented for flexibility)
            // $table->foreign('ipd_id')->references('id')->on('ipd')->onDelete('cascade');
            // $table->foreign('ipd_surgery_id')->references('id')->on('ipd_surgery')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ipd_pre_operative_anaesthesia_evaluation');
    }
};
