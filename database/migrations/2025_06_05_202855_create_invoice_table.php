<?php

use App\Enums\RemovedEnums;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// use App\Enums\Payment\PaymentTypeEnum;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoice', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('patient_id')->nullable();
            $table->uuid('consultation_id')->nullable();
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->float('collected_amount');
            $table->float('balanced_amount');
            $table->string('invoice_number')->unique();
            $table->string('ipd_billing_status')->nullable();
            $table->uuid('ipd_id')->nullable();
            // $table->enum('payment_type', array_column(PaymentTypeEnum::cases(), 'value'))->nullable();
            $table->string('payment_type')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('discount_amount')->nullable();
            $table->string('discount_percentage')->nullable();
            $table->string('patient_name');
            $table->string('patient_email');
            $table->string('patient_phone')->nullable();
            $table->string('patient_number');

            $table->string('doctor_name');
            $table->string('doctor_email');
            $table->string('doctor_phone')->nullable();
            $table->string('additional_amount_reason')->nullable();

            $table->enum('removed', array_column(RemovedEnums::cases(), 'value'))->default(RemovedEnums::Active->value);


            $table->string('referred_by_name')->nullable(); //doctor who refered the patient (from different hospital)
            $table->string('referred_by_phone_no')->nullable(); //doctor who refered the patient (from different hospital)
            $table->string('referred_by_email')->nullable(); //doctor who refered the patient (from different hospital)
            $table->string('referred_by_hospital_name')->nullable(); //doctor who refered the patient (from different hospital)
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('set null');
            $table->foreign('consultation_id')->references('id')->on('consultations')->onDelete('set null');
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('ipd_id')->references('id')->on('ipd')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice');
    }
};
