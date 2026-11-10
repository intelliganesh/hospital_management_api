<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Additive + guarded. Metadata for "General uploads / scanned records" stored in ipd_documents
 * (document_type = 'general_upload'). Existing generated / prefilled documents keep NULLs here.
 */
return new class extends Migration
{
    private array $columns = [
        'category'      => ['string', 50],
        'title'         => ['string', 255],
        'description'   => ['text', null],
        'original_name' => ['string', 255],
        'mime_type'     => ['string', 100],
        'file_size'     => ['unsignedBigInteger', null],
        'uploaded_by'   => ['unsignedBigInteger', null],
        'uploaded_by_name' => ['string', 255],
    ];

    public function up(): void
    {
        if (! Schema::hasTable('ipd_documents')) {
            return;
        }
        foreach ($this->columns as $name => [$type, $len]) {
            if (! Schema::hasColumn('ipd_documents', $name)) {
                Schema::table('ipd_documents', function (Blueprint $table) use ($name, $type, $len) {
                    $col = $len ? $table->$type($name, $len) : $table->$type($name);
                    $col->nullable();
                });
            }
        }
    }

    public function down(): void
    {
        foreach (array_keys($this->columns) as $name) {
            if (Schema::hasColumn('ipd_documents', $name)) {
                Schema::table('ipd_documents', fn (Blueprint $t) => $t->dropColumn($name));
            }
        }
    }
};
