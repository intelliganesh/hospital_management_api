<?php

use App\Enums\AgniEnum;
use App\Enums\AvasthaEnum;
use App\Enums\KoshtaEnum;
use App\Enums\PrakritiEnum;
use App\Enums\RemovedEnums;
use App\Enums\VrikrutiEnum;
use Illuminate\Database\Migrations\Migration;
// use App\Enums\Yoga\DifficultyLevelEnum;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('non_proctology', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // $table->unsignedBigInteger('yoga_asana')->nullable();
            $table->text('doc_upload')->nullable();
            $table->uuid('consultation_id')->nullable(); // FK to consultations
                                                         // $table->text('finding_fields')->nullable();
                                                         // $table->text('food_prescription')->nullable();
                                                         $table->text('diagnosis_summary')->nullable();
                                                         // $table->text('general_advice')->nullable();
                                                         // $table->text('examination_overview')->nullable();
                                                         // $table->text('preliminary_diagnostic')->nullable();

            $table->text('medicines')->nullable();
            $table->text('combination_medicines')->nullable();
            $table->enum('vikruti', array_column(VrikrutiEnum::cases(), 'value'))->nullable();
            $table->enum('prakriti', array_column(PrakritiEnum::cases(), 'value'))->nullable();
            $table->enum('koshta', array_column(KoshtaEnum::cases(), 'value'))->nullable();
            $table->enum('avastha', array_column(AvasthaEnum::cases(), 'value'))->nullable();
            $table->enum('agni', array_column(AgniEnum::cases(), 'value'))->nullable();
            $table->enum('removed', array_column(RemovedEnums::cases(), 'value'))->default(RemovedEnums::Active->value);

            $table->text("yoga_asana")->nullable();
            $table->text("diet_plan")->nullable();
            $table->text("chief_complaints")->nullable();
            $table->text("surgical_history")->nullable();
            $table->text("co_morbidities")->nullable();
            $table->string('co_morbidities_description')->nullable();
            $table->text("on_examination")->nullable();
            $table->text("treatment_plan")->nullable();
            $table->text("tests")->nullable();
            $table->text("additional_cost")->nullable();
            $table->text("food_advice")->nullable();
            $table->text("amount")->nullable();
            // $table->string('asana_name', 100);
            // $table->text('description')->nullable();
            // $table->text('benefits')->nullable();
            // $table->text('contraindications')->nullable();
            // $table->enum('difficulty_level', array_column(DifficultyLevelEnum::cases(), 'value'))->default('Beginner');
            // $table->integer('recommended_duration')->nullable()->comment('Duration in seconds');

            // $table->string('lunch', 255)->nullable();
            // $table->string('dinner', 255)->nullable();
            // $table->string('breakfast', 255)->nullable();

            $table->timestamps();

            // $table->foreign('yoga_asana')->references('id')->on('yoga_asana')->onDelete('set null');
            $table->foreign('consultation_id')->references('id')->on('consultations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('non_proctology');
    }
};
