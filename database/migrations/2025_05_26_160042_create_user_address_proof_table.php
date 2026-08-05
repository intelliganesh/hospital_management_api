<?php

use App\Enums\AddressProofEnum;
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
        Schema::create('user_address_proof', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('image')->nullable();
            $table->enum('id_type', array_column(AddressProofEnum::cases(), 'value'));
            $table->text('id_number_encrypted');               // Encrypted full ID number
            $table->string('id_number_masked');                // Masked for display
            $table->boolean('consent')->default(false);        // Whether consent has been given
            $table->timestamp('consent_given_at')->nullable(); // Timestamp when consent was given
            $table->timestamps();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_address_proof');
    }
};
