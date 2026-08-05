<?php

use App\Enums\AmountForEnums;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('amount_for', function (Blueprint $table) {
            $table->id();
            $table->string("amount_for")->unique();
            $table->text("description")->nullable();
            $table->enum('status', array_column(AmountForEnums::cases(), "value"))->default(AmountForEnums::Inactive->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('amount_for');
    }
};
