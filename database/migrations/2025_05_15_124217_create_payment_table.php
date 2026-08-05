<?php

// use App\Enums\Payment\AmountForEnum;
// use App\Enums\Payment\PaymentTypeEnum;
use App\Enums\Payment\PaymentStatusEnum;
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
        Schema::create('payment', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->float('amount');
            // $table->enum('amount_for', array_column(AmountForEnum::cases(), 'value'));
            $table->string('amount_for');
            $table->text('additional_amount_reason')->nullable();
            $table->string('discount_percentage')->nullable();
            $table->string('discount_amount')->nullable();
            $table->uuid('patient_id')->nullable();
            // $table->string('payment_number')->unique();
            $table->enum('payment_status', array_column(PaymentStatusEnum::cases(), 'value'))->default(PaymentStatusEnum::Pending->value);
            $table->uuid('appointment_id')->nullable();
            $table->uuid('consultation_id')->nullable();
            $table->boolean('include_in_invoice')->default(true);
            // Snapshot of patient info
            $table->string('patient_name');
            $table->string('patient_email')->nullable();
            $table->string('patient_phone')->nullable();
            $table->string('patient_number');
            // Snapshot of doctor info
            $table->string('doctor_name');
            $table->string('doctor_email');
            $table->string('doctor_phone')->nullable();

            // Snapshot of front desk user info
            $table->string('front_desk_user_name');
            $table->string('front_desk_user_email');
            $table->string('front_desk_user_phone')->nullable();

            $table->date('payment_date')->nullable();

            // $table->enum('payment_type', array_column(PaymentTypeEnum::cases(), 'value'))->nullable();
            $table->unsignedBigInteger('front_desk_user_id')->nullable();
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('set null');
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('set null');
            $table->foreign('consultation_id')->references('id')->on('consultations')->onDelete('set null');
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('front_desk_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment');
    }
};
