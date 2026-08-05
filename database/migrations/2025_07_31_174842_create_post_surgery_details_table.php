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
        Schema::create('post_surgery_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->uuid('patient_id')->nullable();
            $table->string('post_surgery_name');
            $table->date('date');
            $table->timestamps();


            // $table->foreign('consultation_id')->references('id')->on('consultations')->onDelete('set null');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_surgery_details');
    }
};
