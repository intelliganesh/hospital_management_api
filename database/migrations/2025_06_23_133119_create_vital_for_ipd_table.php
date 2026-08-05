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
        Schema::create('vital_for_ipd', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ipd_id')->nullable();
            $table->uuid('patient_id')->nullable();
            $table->string('temperature')->nullable();
            $table->string('bp')->nullable();
            $table->string('pulse')->nullable();
            $table->time('time')->nullable();
            $table->string('date')->nullable();
            $table->text('cvs')->nullable(); // Cardiovascular System (observations about the heart and blood vessels.)
            $table->text('rs')->nullable(); // Respiratory System (lung and breathing-related examination notes.)
            $table->timestamps();

            $table->foreign('ipd_id')->references('id')->on('ipd')->onDelete('set null');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vital_for_ipd');
    }
};
