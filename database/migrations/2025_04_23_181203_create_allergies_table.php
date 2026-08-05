<?php

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
        Schema::create('allergies', function (Blueprint $table) {
            $table->id();
            $table->string('allergen_name');
            $table->enum('allergen_type', [
                'Food',
                'Drug',
                'Latex',
                'Plant',
                'Other',
                'Animal',
                'Insect',
                'Vaccine',
                'Chemical',
                'Environmental',
            ])->nullable();
            $table->string('other_allergen_type')->nullable();
            // $table->string('reaction_type');
            // $table->enum('severity', ['Mild', 'Moderate', 'Severe']);
            // $table->date('date_first_experienced')->nullable();
            // $table->text('management')->nullable();
            $table->enum('department_type', array_column(TypeEnum::cases(), 'value'));
            $table->string('documented_by');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allergies');
    }
};
