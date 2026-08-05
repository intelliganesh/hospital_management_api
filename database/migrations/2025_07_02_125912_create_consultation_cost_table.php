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
        Schema::create('consultation_cost', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string("consultation_name")->unique();
            $table->decimal('amount', 10, 2);
            $table->enum('department_type', array_column(TypeEnum::cases(), 'value'));
            $table->enum('status', array_column(ComanStatusEnum::cases(), 'value'))->default(ComanStatusEnum::Active->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultation_cost');
    }
};
