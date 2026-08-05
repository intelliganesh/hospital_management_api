<?php

use App\Enums\RemovedEnums;
use App\Enums\YesOrNoStatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('proctology', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('consultation_id')->nullable(); // FK to consultations
            // $table->unsignedBigInteger('test_id')->nullable(); // FK to tests_master
            $table->text('medicines')->nullable();
            $table->text('combination_medicines')->nullable();
            $table->text('doc_upload')->nullable();
            $table->string('dre_secondary_position')->nullable();
            $table->string('proctoscopy_secondary_position')->nullable();
            $table->string('proctoscopy_anal_polyp_at')->nullable();
            $table->text('finding_fields')->nullable();
            $table->text('advice_field')->nullable();
            $table->text('diagnosis_summary')->nullable();
            $table->text('examination_overview')->nullable();
            $table->text('preliminary_diagnostic')->nullable();
            $table->text('proctoscopy')->nullable();
            $table->string('dre_induration_at')->nullable();
            $table->text('dre')->nullable();
            $table->float('discount_amount')->nullable();
            $table->string('co_morbidities_description')->nullable();
            $table->enum('removed', array_column(RemovedEnums::cases(), 'value'))->default(RemovedEnums::Active->value);
            $table->enum('previous_scar', array_column(YesOrNoStatusEnum::cases(), 'value'))->nullable();
            $table->enum('abscess', array_column(YesOrNoStatusEnum::cases(), 'value'))->nullable();
            $table->string('previous_scar_position')->nullable();
            $table->string('secondary_anal_valve')->nullable();
            $table->string('anal_valve')->nullable();
            $table->text('secondary_opening_position')->nullable();
            $table->text('internal_opening_position')->nullable();
            $table->text('internal_opening_distance')->nullable();
            $table->text('type_of_fistula_position')->nullable();
            $table->text('type_of_fistula_sphincter')->nullable();
            $table->text('no_of_tracks_in_one_fistula')->nullable();
            $table->string('posterior_fistulous_angle')->nullable();
            $table->string('no_of_fistula')->nullable();
            $table->enum('fistula_recurrence', ['new_case', 'recurrence'])->default('new_case');
            $table->integer('fistula_recurrence_surgery_count')->default(0);
            $table->text('fistula_remark')->nullable();
            $table->string('managements')->nullable();
            $table->string('consultation_discount')->nullable();
            $table->date('managements_date')->nullable();
            $table->string('abscess_position')->nullable();
            $table->text('external_opening_position')->nullable();
            $table->text('no_of_external_opening_position')->nullable();
            $table->text('any_other')->nullable();
            $table->text('no_of_secondary_opening_position')->nullable();
            $table->text('type_of_crypt')->nullable();
            $table->text('crypt_cause')->nullable();
            $table->text('basis_of_high_low_riding')->nullable();
            $table->text('distant_visceral_communication')->nullable();
            $table->string('sono_fistula_gram')->nullable();
            $table->string('mri_fistula_gram')->nullable();

            $table->string('sonologist')->nullable();
            $table->text('sonologist_findings')->nullable();
            $table->text('other_investigation')->nullable();
            $table->text("diet_plan")->nullable();
            $table->text("chief_complaints")->nullable();
            $table->text("surgical_history")->nullable();
            $table->text("co_morbidities")->nullable();
            $table->text("on_examination")->nullable();
            $table->text("treatment_plan")->nullable();
            $table->text("tests")->nullable();
            $table->text("additional_cost")->nullable();
            // $table->text("food_advice")->nullable();
            $table->text("amount")->nullable();

            $table->timestamps();

            // $table->foreign('test_id')->references('id')->on('tests')->onDelete('set null');
            $table->foreign('consultation_id')->references('id')->on('consultations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proctology');
    }
};
