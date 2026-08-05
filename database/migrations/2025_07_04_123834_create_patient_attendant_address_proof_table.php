<?php

use App\Enums\AddressProofEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('patient_attendant_address_proof', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('patient_id');
            $table->string('image')->nullable();
            $table->string('id_proof_for_pan')->nullable();
            $table->enum('id_type', array_column(AddressProofEnum::cases(), 'value'));
            $table->text('id_number_encrypted'); // Encrypted full ID number
            $table->string('id_number_masked');  // Masked for display
            $table->boolean('consent')->default(false); // Whether consent has been given
            $table->timestamp('consent_given_at')->nullable(); // Timestamp when consent was given
            $table->timestamps();
            $table->foreign('patient_id')
                ->references('id')
                ->on('patients')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_attendant_address_proof');
    }
};
