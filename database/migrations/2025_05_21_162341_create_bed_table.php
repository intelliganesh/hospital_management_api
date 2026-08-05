<?php

use App\Enums\BedSizeEnum;
use App\Enums\BedTypeEnum;
use App\Enums\BedStatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bed', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('room_id')->nullable();
            $table->string('bed_number');
            $table->unique('bed_number');
            $table->string('description')->nullable();
            $table->enum('bed_type', array_column(BedTypeEnum::cases(), 'value'));
            $table->enum('status', array_column(BedStatusEnum::cases(), 'value'))->default(BedStatusEnum::Available->value);
            $table->timestamps();
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bed');
    }
};
