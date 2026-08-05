<?php

use App\Enums\Payment\PaymentTypeEnum;
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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_number')->unique()->nullable();
            $table->date('date');                // For expense date
                                                 // $table->string('proof')->nullable(); // File path or filename (nullable)
            $table->string('image')->nullable(); // File path or filename (nullable)
            $table->string('for_name')->nullable();
            $table->string('entered_name')->nullable();
            $table->decimal('amount', 10, 2);                                                 // Adjust precision/scale as needed
            $table->string('description');                                                    // Corrected from 'discription'
            $table->string('expense_name');                                                   // Name/title of expense
            $table->string('transaction_id')->nullable();                                     // Optional transaction ID
            $table->enum('mode_of_payment', array_column(PaymentTypeEnum::cases(), 'value')); // e.g., cash, card, etc.
            $table->string('other')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
