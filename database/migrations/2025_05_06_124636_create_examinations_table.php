<?php

use App\Enums\RemovedEnums;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // only text fields
        Schema::create('examinations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('patient_id')->nullable();
            $table->uuid('appointment_id')->nullable();
            $table->uuid('consultation_id')->nullable();
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->string('doctor_name');
            $table->string('doctor_email');
            $table->string('doctor_phone')->nullable();
            $table->string('patient_number');
            $table->string('patient_name');
            $table->string('patient_email');
            // $table->string('temperature')->nullable();
            // $table->string('bp')->nullable();
            // $table->string('pulse')->nullable();
            // $table->text('cvs')->nullable();// Cardiovascular System (observations about the heart and blood vessels.)
            // $table->text('rs')->nullable(); // Respiratory System (lung and breathing-related examination notes.)
            $table->text('description')->nullable();
            $table->text('examination_overview')->nullable();
            $table->enum('removed', array_column(RemovedEnums::cases(), 'value'))->default(RemovedEnums::Active->value);
            // $table->uuid('examination_categories_id');
            // $table->text('complaint')->nullable();
            // $table->text('advice')->nullable();
            // $table->text('preliminary_diagnosis')->nullable();
            // $table->date('next_visit_date');
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('set null');
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('set null');
            $table->foreign('consultation_id')->references('id')->on('consultations')->onDelete('set null');
            // $table->foreign('examination_categories_id')->references('id')->on('examination_categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examinations');
    }
};
