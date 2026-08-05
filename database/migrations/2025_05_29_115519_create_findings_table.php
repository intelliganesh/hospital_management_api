<?php

use App\Enums\FindingsCategoryEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('findings', function (Blueprint $table) {
            $table->id();
            $table->string('findings_number', 50)->unique();
            $table->string('finding_name', 100);
            // $table->string('findings_number')->unique();
            $table->string('finding_description')->nullable();
            $table->enum('category', array_column(FindingsCategoryEnum::cases(), 'value'));
            $table->enum('status', ['Active', 'Inactive']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('findings');
    }
};
