<?php

// use App\Enums\DepartmentTypeEnum;
use App\Enums\Consultation\TypeEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');                  // Department name (e.g., Cardiology, HR)
            $table->string('code')->nullable();      // Optional code (e.g., HR001)
            $table->text('description')->nullable(); // Optional description
                                                     // $table->enum('department_type', array_column(DepartmentTypeEnum::cases(), 'value'));
            $table->enum('department_type', array_column(TypeEnum::cases(), 'value'));
                                                         // $table->enum('type_of_department', array_column(TypeEnum::cases(), 'value'))->default(TypeEnum::Allopathy->value);
            $table->boolean('is_active')->default(true); // Active status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
