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
        Schema::create('family_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('patient_id')->nullable();
            $table->string('ipd_number')->unique();
            $table->string('opd_number')->nullable();
            $table->enum('relationship', ['Father', 'Mother', 'Brother', 'Sister', 'Uncle', 'Aunt', 'Grandfather', 'Grandmother', 'Other'])->nullable();
            $table->string('name');
            $table->integer('age');
            $table->boolean('living_status');
            $table->integer('age_at_death')->nullable();
            $table->string('cause_of_death')->nullable();
            $table->text('additional_notes')->nullable();
            $table->string('documented_by');
            $table->unsignedBigInteger('documented_by_id')->nullable();
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('set null');
            $table->foreign('documented_by_id')->references('id')->on('users')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_history');
    }
};
