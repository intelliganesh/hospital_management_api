<?php

use App\Enums\WardTypeEnum;
use App\Enums\WardStatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ward', function (Blueprint $table) {
            $table->id();
            $table->string('ward_number');
            $table->unique('ward_number');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->enum('type', array_column(WardTypeEnum::cases(), 'value'))->nullable();
            $table->string('floor', 10)->nullable();
            $table->enum('status', array_column(WardStatusEnum::cases(), 'value'))->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ward');
    }
};
