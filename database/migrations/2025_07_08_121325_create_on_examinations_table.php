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
        Schema::create('on_examinations', function (Blueprint $table) {
            $table->id();
            $table->text("finding");
            $table->text('normal_range')->nullable();
            $table->text('examination_type')->nullable();
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
        Schema::dropIfExists('on_examinations');
    }
};
