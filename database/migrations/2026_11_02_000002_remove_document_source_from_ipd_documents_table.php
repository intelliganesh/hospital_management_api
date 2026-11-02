<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('ipd_documents', 'document_source')) {
            return;
        }

        Schema::table('ipd_documents', function (Blueprint $table) {
            $table->dropColumn('document_source');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('ipd_documents', 'document_source')) {
            return;
        }

        Schema::table('ipd_documents', function (Blueprint $table) {
            $table->string('document_source', 20)
                ->default('generated')
                ->after('document_path');
        });
    }
};
