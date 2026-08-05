<?php

use App\Enums\LocationEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ward_id');
            $table->string('room_number');
            $table->unique('room_number');
            $table->string('description')->nullable();
            $table->string('name', 100);
            $table->string('room_type', 100);
            $table->string('floor', 10)->nullable();
            $table->integer('bed_count')->default(0);
            $table->string('status', 100);
            $table->foreign('ward_id')->references('id')->on('ward')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
