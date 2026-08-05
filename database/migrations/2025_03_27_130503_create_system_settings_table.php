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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('hospital_logo')->nullable();
            $table->string('letter_header')->nullable();
            $table->text('billing_letter_header')->nullable();
            $table->integer('invoice_start_number')->nullable();
            $table->boolean('invoice_status')->nullable();
            $table->string('hospital_name', 225);
            $table->text('address');
            $table->char('hospital_prefix', 10);
            $table->char('findings_prefix', 10);
            $table->char('invoice_prefix', 10);
            $table->char('opd_prefix', 10);
            $table->char('ipd_prefix', 10);
            $table->char('patient_prefix', 10);
            $table->char('appointment_prefix', 10);
            $table->char('payment_prefix', 10);
            $table->char('test_prefix', 10);
            $table->char('code_prefix', 10);
            $table->char('food_prefix', 10);
            $table->char('ward_prefix', 10);
            $table->char('room_prefix', 10);
            $table->char('bed_prefix', 10);
            $table->char('currency_symbol', 1);
            $table->boolean('email_notification')->default(0);
            $table->char('currency', 3);
            $table->string('upi')->nullable();
            $table->string('qr_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
