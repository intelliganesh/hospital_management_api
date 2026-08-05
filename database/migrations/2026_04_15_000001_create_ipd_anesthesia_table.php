<?php

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
        Schema::create('ipd_anaesthesia', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ipd_id');
            $table->uuid('ipd_surgery_id');
            $table->text('diagnosis')->nullable();
            $table->string('position')->nullable();
            $table->string('anaesthetist_assistant')->nullable();
            $table->string('type_of_anaesthesia')->nullable();
            $table->string('uploaded_consent_path')->nullable();
            $table->text('consent_summary')->nullable();
            $table->string('upload_anaesthesia_record_path')->nullable();
            $table->text('anaesthesia_record_summary')->nullable();
            $table->dateTime('datetime')->nullable();
            $table->decimal('patient_height', 5, 2)->nullable();
            $table->decimal('patient_weight', 6, 2)->nullable();
            $table->string('patient_community')->nullable();
            $table->string('patient_mother_tongue')->nullable();
            $table->timestamps();

            // Foreign key constraints (commented for flexibility)
            // $table->foreign('ipd_id')->references('id')->on('ipd')->onDelete('cascade');
            // $table->foreign('surgery_id')->references('id')->on('ipd_surgery')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ipd_anaesthesia');
    }
};
