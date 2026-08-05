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
        Schema::create('patient_tests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('consultation_id')->nullable(); // FK to consultations
            $table->unsignedBigInteger('test_id')->nullable(); // FK to tests_master
            $table->string('test_name');
            $table->uuid('patient_id')->nullable();
            $table->text('test_description')->nullable();
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->string('test_place'); // e.g., same hospital or other hospital
            $table->integer('billing_amount')->nullable()->default(0);
            $table->enum('result_status', ['Pending', 'Started', 'Completed'])->default('Pending');
            $table->unsignedBigInteger('result_uploaded_by')->nullable();
            $table->text('document_upload')->nullable();

            // Snapshot of patient info
            $table->string('patient_name');
            $table->string('patient_email');
            $table->string('patient_phone')->nullable();
            $table->string('patient_number');
            // Snapshot of doctor info
            $table->string('doctor_name');
            $table->string('doctor_email');
            $table->string('doctor_phone')->nullable();

            $table->timestamps();

            // Foreign Keys
            $table->foreign('consultation_id')->references('id')->on('consultations')->onDelete('set null');
            $table->foreign('test_id')->references('id')->on('tests')->onDelete('set null');
            $table->foreign('result_uploaded_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_tests');
    }
};
