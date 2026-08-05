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
        Schema::create('ipd_nurse_notes', function (Blueprint $table) {
             $table->uuid('id')->primary();
            $table->uuid('ipd_id');
            $table->unsignedBigInteger('nurse_id');
            $table->string('nurse_name')->nullable();
            $table->string('nurse_phone')->nullable();
            $table->string('bp')->nullable();
            $table->string('spo2')->nullable();
            $table->string('temperature')->nullable();
            $table->string('pulse')->nullable();
            $table->text('remark1')->nullable();
            $table->text('remark2')->nullable();
            $table->dateTime('datetime')->nullable();
            $table->timestamps();
            
            // Foreign key constraints (optional - uncomment if references exist)
            // $table->foreign('ipd_id')->references('id')->on('ipd')->onDelete('cascade');
            // $table->foreign('nurse_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nurse_notes');
    }
};
