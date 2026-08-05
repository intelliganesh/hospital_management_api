<?php

use App\Enums\RemovedEnums;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vitals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('consultation_id')->nullable();
            $table->string('temperature')->nullable();
            $table->string('bp')->nullable();
            $table->string('pulse')->nullable();
            $table->enum('removed', array_column(RemovedEnums::cases(), 'value'))->default(RemovedEnums::Active->value);
            $table->text('cvs')->nullable();// Cardiovascular System (observations about the heart and blood vessels.)
            $table->text('rs')->nullable(); // Respiratory System (lung and breathing-related examination notes.)
            $table->timestamps();

            $table->foreign('consultation_id')->references('id')->on('consultations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vitals');
    }
};
