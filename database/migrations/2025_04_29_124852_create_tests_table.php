<?php

use App\Enums\Consultation\TypeEnum;
// use App\Enums\DepartmentTypeEnum;
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
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->string('test_name');
            $table->string('test_number')->unique();
            $table->float('test_price')->nullable();
            $table->float('tax_price')->nullable();
            $table->enum('department_type', array_column(TypeEnum::cases(), 'value'))->nullable();
            $table->text('test_description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tests');
    }
};
