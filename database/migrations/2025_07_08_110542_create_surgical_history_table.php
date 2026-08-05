<?php

use App\Enums\ComanStatusEnum;
use App\Enums\Consultation\TypeEnum;
// use App\Enums\DepartmentTypeEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('surgical_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string("surgery_name", 255);
            $table->text('description')->nullable();
            $table->enum('department_type', array_column(TypeEnum::cases(), 'value'));
            $table->enum('is_active', array_column(ComanStatusEnum::cases(), 'value'))->default(ComanStatusEnum::Active->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surgical_history');
    }
};
