<?php

use App\Enums\Consultation\TypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('_d_r_e', function (Blueprint $table) {
            $table->id();
            $table->string('dre_name')->nullable();
            $table->enum('department_type', array_column(TypeEnum::cases(), 'value'));
            // $table->string('internal_opening')->nullable();
            // $table->json('hemorrhoid_positions')->nullable(); // e.g., ["3 o'clock", "7 o'clock"]
            // $table->string('field_induration_at')->nullable(); // e.g., "7 o'clock"
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_d_r_e');
    }
};
