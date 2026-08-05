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
        Schema::create('ipd_doctor_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ipd_id');
            $table->unsignedBigInteger('doctor_id');
             $table->string('doctor_name')->nullable();
            $table->string('doctor_phone')->nullable();
            $table->dateTime('datetime')->nullable();
            $table->string('gc')->nullable()->comment('General Condition');
            $table->string('bp')->nullable()->comment('Blood Pressure');
            $table->string('pr')->nullable()->comment('Pulse Rate');
            $table->text('clinical_notes')->nullable();
            $table->text('diagnosis')->nullable();
            $table->timestamps();
            
            // Foreign key constraints (optional - uncomment if references exist)
            // $table->foreign('ipd_id')->references('id')->on('ipd')->onDelete('cascade');
            // $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ipd_doctor_notes');
    }
};
