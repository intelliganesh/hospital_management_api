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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('consultation_id')->nullable();
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->uuid('patient_id')->nullable();

            // Patient & Doctor details
            $table->string('patient_number');
            $table->string('patient_name');
            $table->string('doctor_name');
            $table->string('patient_email');
            $table->string('doctor_email');
            $table->string('patient_phone');
            $table->string('doctor_phone');

            $table->string('medicine_ids');
            $table->string('medicine_name');
            $table->string('dosage');
            $table->string('duration');
            $table->string('time');
            $table->text('food_advice');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('consultation_id')
                ->references('id')->on('consultations')->onDelete('set null');
            $table->foreign('doctor_id')
                ->references('id')->on('users')->onDelete('set null');
            $table->foreign('patient_id')
                ->references('id')->on('patients')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
