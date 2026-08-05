<?php

use App\Enums\ComanStatusEnum;
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
        Schema::create('managements', function (Blueprint $table) {
            $table->id();
            $table->string('management_name');
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
        Schema::dropIfExists('managements');
    }
};
