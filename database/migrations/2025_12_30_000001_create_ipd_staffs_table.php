<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ipd_staffs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ipd_id');
            $table->unsignedBigInteger('user_id');
            $table->string('user_name')->nullable();
            $table->string('user_phone')->nullable();
            $table->enum('user_role', ['consultant_doctor', 'duty_doctor', 'nurse']);
            $table->enum('shift', ['morning', 'afternoon', 'evening', 'night', 'on_call','off_duty'])->nullable();
            $table->dateTime('assigned_date');
            $table->timestamps();

            /* Foreign key constraints */
            $table->foreign('ipd_id')->references('id')->on('ipd')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            /* Indexes for faster queries */
            $table->index('ipd_id');
            $table->index('user_id');
            $table->index('user_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ipd_staffs');
    }
};
