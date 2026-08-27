<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ipd_discharge_summary', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ipd_id');
            $table->text('doctor_incharge')->nullable();
            $table->text('consultants')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('case_history_and_complaints')->nullable();
            $table->text('general_examination')->nullable();
            $table->text('systemic_examination')->nullable();
            $table->text('investigations')->nullable();
            $table->text('operation_done')->nullable();
            $table->text('findings_and_procedure')->nullable();
            $table->text('course_in_hospital')->nullable();
            $table->text('patient_health_condition_at_discharge')->nullable();
            $table->text('advice_on_discharge')->nullable();
            $table->text('medicines')->nullable();
            $table->text('combination_medicines')->nullable();
            $table->text('tests')->nullable();
            $table->text('diet_plan')->nullable();
            $table->text('special_instruction')->nullable();
            $table->string('upload_pdf_path')->nullable();
            $table->timestamps();

            // $table->foreign('ipd_id')->references('id')->on('ipd')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ipd_discharge_summary');
    }
};
