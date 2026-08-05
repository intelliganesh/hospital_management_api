<?php

use Illuminate\Support\Facades\Schema;
use App\Enums\Yoga\DifficultyLevelEnum;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('yoga_asana', function (Blueprint $table) {
            $table->id();
            $table->string('asana_name', 100);
            $table->text('description')->nullable();
            $table->text('benefits')->nullable();
            $table->text('contraindications')->nullable();
            $table->enum('difficulty_level', array_column(DifficultyLevelEnum::cases(), 'value'))->default('Beginner');
            $table->integer('recommended_duration')->nullable()->comment('Duration in seconds');
            $table->enum('status', ['Active', 'Inactive']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('yoga_asana');
    }
};
