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
        Schema::create('theme', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->unsignedBigInteger('system_settings_id');
            $table->string('primary_color', 20)->nullable();
            $table->string('text_primary_color', 20)->nullable();
            $table->string('bg_primary_color', 20);
            $table->string('secondary_color', 20)->nullable();
            $table->string('text_secondary_color', 20)->nullable();
            $table->string('bg_secondary_color', 20);
            $table->string('tertiary_color', 20)->nullable();
            $table->string('text_tertiary_color', 20)->nullable();
            $table->string('bg_tertiary_color', 20);
            $table->enum('theme', ['dark', 'light', 'system']);
            $table->timestamps();

            $table->foreign('system_settings_id')->references('id')->on('system_settings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('theme');
    }
};
