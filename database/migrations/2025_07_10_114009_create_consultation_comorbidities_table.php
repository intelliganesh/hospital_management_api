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
        Schema::create('consultation_comorbidities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string("name", 255);
            $table->text('description')->nullable();
            $table->boolean("is_chronic");
            $table->uuid("consultation_id")->nullable();
            $table->unsignedBigInteger("comorbidities_id")->nullable();
            $table->timestamps();

            $table->foreign('consultation_id')->references('id')->on('consultations')->onDelete('set null');
            $table->foreign('comorbidities_id')->references('id')->on('comorbidities')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultation_comorbidities');
    }
};
