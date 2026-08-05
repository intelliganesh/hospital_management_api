<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('patient_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('patient_id');
            $table->string('document_name'); // e.g., "Medical Report", "Lab Test", "Prescription"
            $table->text('document_path'); // Comma-separated paths for multiple files
            $table->date('document_date')->nullable(); // Date of the document
            $table->string('uploaded_by')->nullable(); // User who uploaded the document
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('patient_id')
                ->references('id')
                ->on('patients')
                ->onDelete('cascade');
            
            // Index for faster queries
            $table->index('patient_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_documents');
    }
};
