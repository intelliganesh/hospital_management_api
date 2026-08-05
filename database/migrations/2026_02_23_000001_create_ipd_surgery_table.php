<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ipd_surgery', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ipd_id')->unique();
            $table->string('surgery_name');
            $table->string('surgery_type');
            $table->date('surgery_date');
            $table->string('status')->nullable();
            $table->string('surgeon')->nullable();
            $table->string('anaesthetist')->nullable();
            $table->string('department')->nullable();
            $table->dateTime('surgery_start_datetime')->nullable();
            $table->dateTime('surgery_end_datetime')->nullable();
            $table->string('assistant_surgeon')->nullable();
            $table->string('scrub_nurse')->nullable();
            $table->text('specimen_for_hpe')->nullable();
            $table->text('operative_notes')->nullable();
            $table->text('operative_findings')->nullable();
            $table->text('post_operative_instructions')->nullable();
            $table->text('summary')->nullable();
            $table->text('consent_summary')->nullable();
            $table->string('uploaded_report_path')->nullable();
            $table->string('uploaded_consent_path')->nullable();
            $table->timestamps();
            
            // Foreign key constraints (optional - uncomment if references exist)
            // $table->foreign('ipd_id')->references('id')->on('ipd')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ipd_surgery');
    }
};
