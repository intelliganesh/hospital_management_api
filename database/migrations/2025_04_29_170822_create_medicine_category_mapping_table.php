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
        Schema::create('medicine_category_mapping', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('medicine_id')->nullable();
            $table->timestamps();

            // Foreign keys without cascade delete
            $table->foreign('category_id')->references('id')->on('medicine_categories')->onDelete('set null');
            $table->foreign('medicine_id')->references('id')->on('medicines')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_category_mapping');
    }
};
