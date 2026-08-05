<?php

use App\Enums\Consultation\TypeEnum;
use App\Enums\Medicine\DosageFormEnum;
use Illuminate\Support\Facades\Schema;
use App\Enums\Medicine\StrengthUnitEnum;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->date('expiry_date')->nullable();
            // $table->integer('stock_quantity')->default(0);
            // alter table comorbidities add COLUMN department_type enum('Proctology','Non Proctology','Allopathy')
            $table->enum("department_type", array_column(TypeEnum::cases(), 'value'));
            $table->string('medicine_name', 100);
            $table->integer('stock_quantity')->nullable();
            // $table->decimal('strength_value', 10, 2)->nullable();
            $table->string('strength', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('generic_name', 100)->nullable();
            $table->string('manufacturer', 100)->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->enum('dosage_form', array_column(DosageFormEnum::cases(), 'value'));
            $table->enum('strength_unit', array_column(StrengthUnitEnum::cases(), 'value'))->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
