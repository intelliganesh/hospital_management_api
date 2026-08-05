<?php

use App\Enums\AppointmentTypeEnum;
use App\Enums\Appointment\StatusEnum;
use Illuminate\Support\Facades\Schema;
use App\Enums\Payment\PaymentStatusEnum;
use App\Enums\ConsultationTypeEnum;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('appointment_number')->unique();
            $table->uuid('patient_id')->nullable();
            $table->unsignedBigInteger('front_desk_user_id')->nullable();
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->text('complaint')->nullable();
            // $table->enum('type', ['First Visit', 'Follow-up']);
            $table->enum('type', array_column(AppointmentTypeEnum::cases(), 'value'));
            $table->enum('consultation_type', array_column(ConsultationTypeEnum::cases(), 'value'))->default(ConsultationTypeEnum::Offline->value);
            // $table->float('appointment_fees');
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

            $table->string('referred_by_name')->nullable(); //doctor who refered the patient (from different hospital)
            $table->string('referred_by_phone_no')->nullable(); //doctor who refered the patient (from different hospital)
            $table->string('referred_by_email')->nullable(); //doctor who refered the patient (from different hospital)
            $table->string('referred_by_hospital_name')->nullable(); //doctor who refered the patient (from different hospital)

            $table->date('appointment_date')->nullable();
            $table->time('appointment_time')->nullable();
            $table->enum('payment_status', array_column(PaymentStatusEnum::cases(), 'value'))->default(PaymentStatusEnum::Pending->value);
            $table->enum('status', array_column(StatusEnum::cases(), 'value'))->default(StatusEnum::Pending->value);
            $table->timestamps();
            // Foreign key constraints without cascading deletes
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('set null');
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('front_desk_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
