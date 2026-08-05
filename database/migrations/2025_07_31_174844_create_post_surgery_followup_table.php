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
        Schema::create('post_surgery_followup', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('consultation_id')->nullable();
            $table->uuid('post_surgery_details_id')->nullable();
            $table->string('appointment_number')->nullable();
            $table->date('date')->default(date('Y-m-d'));
            $table->string('dressing')->nullable();
            $table->string('ks_changed')->nullable();
            $table->string('cut_through')->nullable();
            $table->string('partial_lay_open')->nullable();
            $table->string('follow_up_examination')->nullable();
            $table->string('new_abscess_threading')->nullable();
            $table->timestamps();

            $table->foreign('consultation_id')->references('id')->on('consultations')->onDelete('set null');
            $table->foreign('post_surgery_details_id')->references('id')->on('post_surgery_details')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_surgery_followup');
    }
};
