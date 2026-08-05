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
        Schema::create('proctoscopys', function (Blueprint $table) {
            $table->id();
            $table->string('proctoscopys_name')->nullable();
            $table->enum('department_type', array_column(TypeEnum::cases(), 'value'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proctoscopys');
    }
};
