<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ipd_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ipd_id');
            $table->uuid('ipd_surgery_id')->nullable();
            $table->string('document_type');
            $table->string('document_path');
            $table->timestamps();

            /* Foreign key constraints */
            $table->foreign('ipd_id')->references('id')->on('ipd')->onDelete('cascade');
            $table->foreign('ipd_surgery_id')->references('id')->on('ipd_surgery')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ipd_documents');
    }
};
