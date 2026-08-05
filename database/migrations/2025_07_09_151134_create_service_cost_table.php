<?php

use App\Enums\ComanStatusEnum;
use App\Enums\DepartmentTypeEnum;
use App\Enums\Consultation\TypeEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_cost', function (Blueprint $table) {
            $table->id();
            $table->decimal("cost");
            $table->string("service_name")->unique();
            $table->string("description")->nullable();
            $table->enum('department_type', array_column(TypeEnum::cases(), 'value'));
            $table->enum('case_type', array_column(DepartmentTypeEnum::cases(), 'value'));
            $table->enum('status', array_column(ComanStatusEnum::cases(), 'value'))->default(ComanStatusEnum::Active->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_cost');
    }
};
