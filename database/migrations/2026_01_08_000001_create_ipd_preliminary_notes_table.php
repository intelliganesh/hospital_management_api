<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ipd_preliminary_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ipd_id');

            /* Chief and Associated Complaints */
            $table->text('chief_complaint')->nullable();
            $table->text('associated_medical_illness')->nullable();

            /* History */
            $table->text('previous_treatment_history')->nullable();
            $table->text('medical_history')->nullable();
            $table->text('family_history')->nullable();
            $table->text('personal_history')->nullable();
            $table->text('allergy')->nullable();

            /* Vital Signs */
            $table->string('bp', 50)->nullable();
            $table->string('pulse', 50)->nullable();
            $table->string('temperature', 50)->nullable();
            $table->string('spo2', 50)->nullable();
            $table->string('weight', 50)->nullable();
            $table->string('height', 50)->nullable();

            /* Physical Examination */
            $table->string('cvs')->nullable();
            $table->string('rs')->nullable();
            $table->text('per_abdomen')->nullable();
            $table->text('local_examination')->nullable();
            $table->text('pr')->nullable();
            $table->text('dre')->nullable();
            $table->text('proctoscopy')->nullable();
            $table->text('examination_comments')->nullable();

            /* Investigation */
            $table->text('investigation')->nullable();
            $table->string('hb', 50)->nullable();
            $table->string('tc', 50)->nullable();
            $table->string('esr', 50)->nullable();
            $table->string('rbs', 50)->nullable();
            $table->string('bt', 50)->nullable();
            $table->string('ct', 50)->nullable();
            $table->string('blood_urea', 50)->nullable();
            $table->string('hiv', 50)->nullable();
            $table->string('hbsag', 50)->nullable();

            /* Diagnosis and Treatment */
            $table->text('line_of_treatment')->nullable();
            $table->text('provisional_diagnosis')->nullable();
            $table->text('final_diagnosis')->nullable();
            $table->text('treatment_advised')->nullable();
            $table->text('treatment_given')->nullable();
            $table->text('preoperative_instruction')->nullable();

            $table->timestamps();

            /* Foreign key constraints */
            $table->foreign('ipd_id')->references('id')->on('ipd')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ipd_preliminary_notes');
    }
};
