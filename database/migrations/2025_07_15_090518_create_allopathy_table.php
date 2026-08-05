<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('allopathy', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('consultation_id')->nullable(); // FK to consultations
            // $table->unsignedBigInteger('test_id')->nullable(); // FK to tests_master
            $table->text('medicines')->nullable();
            $table->text('doc_upload')->nullable();
            $table->text('finding_fields')->nullable();
            $table->text('advice_field')->nullable();
            $table->text('diagnosis_summary')->nullable();
            $table->text('examination_overview')->nullable();
            $table->text('preliminary_diagnostic')->nullable();
            $table->text('proctoscopy')->nullable();
            $table->string('dre_secondary_position')->nullable();
            $table->string('proctoscopy_secondary_position')->nullable();
            $table->string('proctoscopy_anal_polyp_at')->nullable();
            $table->string('dre_induration_at')->nullable();
            $table->text('dre')->nullable();

            $table->text("diet_plan")->nullable();
            $table->text("chief_complaints")->nullable();
            $table->text("surgical_history")->nullable();
            $table->text("co_morbidities")->nullable();
            $table->text("on_examination")->nullable();
            $table->text("treatment_plan")->nullable();
            $table->text("tests")->nullable();
            $table->text("additional_cost")->nullable();
            $table->text("amount")->nullable();
            // $table->text("food_advice")->nullable();

            $table->timestamps();

            // $table->foreign('test_id')->references('id')->on('tests')->onDelete('set null');
            $table->foreign('consultation_id')->references('id')->on('consultations')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allopathy');
    }
};
