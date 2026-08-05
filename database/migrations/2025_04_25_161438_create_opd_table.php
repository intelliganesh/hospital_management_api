<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOpdTable extends Migration
{
    public function up(): void
    {
        Schema::create('opd', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('opd_number')->unique();
            $table->uuid('patient_id')->nullable();
            // $table->uuid('converted_to_ipd_id')->nullable();
            $table->uuid('appointment_id')->nullable();
            $table->enum('status', ['Pending', 'Completed', 'Converted to IPD', 'Cancelled'])->default('Pending');
            $table->dateTime('visit_date');
            $table->text('complaint')->nullable();
            $table->unsignedBigInteger('referred_to_doctor_id')->nullable();
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('set null');
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('set null');
            $table->foreign('referred_to_doctor_id')->references('id')->on('users')->onDelete('set null');
            // $table->foreign('converted_to_ipd_id')->references('id')->on('ipd_cases');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opd');
    }
}
