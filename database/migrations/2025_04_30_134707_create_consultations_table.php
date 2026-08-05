<?php

use App\Enums\AppointmentTypeEnum;
use App\Enums\Appointment\StatusEnum;
use App\Enums\Consultation\TypeEnum;
use App\Enums\Payment\PaymentStatusEnum;
use App\Enums\RemovedEnums;
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
        Schema::create('consultations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('appointment_id')->nullable();
            $table->string('appointment_number');
            $table->string('patient_id')->nullable();
            $table->string('medical_id')->nullable();
            $table->enum('removed', array_column(RemovedEnums::cases(), 'value'))->default(RemovedEnums::Active->value);
            $table->string('test_id')->nullable(); // FK to tests_master
                                                   //need to have medicine table appointment id, patient id, medicine names, dosages, time when to take, doctor id. after appointment completed need to hide edit and delete in appointment table
                                                   // $table->string('patient_number');
            $table->text('complaint')->nullable();
            $table->enum("appointment_type", array_column(AppointmentTypeEnum::cases(), 'value'))->default(AppointmentTypeEnum::FirstVisit->value);
            $table->float('fees');
            $table->float('surgical_cost')->nullable();
            $table->boolean('advice_admition')->default(false);
            $table->boolean('test_in_same_hospital')->default(false);
            // $table->string('doctor_name');
            $table->unsignedBigInteger('doctor_id')->nullable();
            $table->unsignedBigInteger('front_desk_user_id')->nullable();
            $table->text('preliminary_diagnosis')->nullable();
            $table->text('advice')->nullable();
            $table->date('next_visit_date')->nullable();
            $table->date('advice_admition_date')->nullable();
            $table->enum('type', array_column(TypeEnum::cases(), 'value'))->default(TypeEnum::None->value);
            // Snapshot of patient info
            $table->string('patient_name');
            $table->string('patient_email');
            $table->string('patient_phone')->nullable();
            $table->string('patient_number');
            // Snapshot of doctor info
            $table->string('doctor_name');
            $table->string('doctor_email');
            $table->string('doctor_phone')->nullable();

            $table->string('referred_by_name')->nullable();          //doctor who refered the patient (from different hospital)
            $table->string('referred_by_phone_no')->nullable();      //doctor who refered the patient (from different hospital)
            $table->string('referred_by_email')->nullable();         //doctor who refered the patient (from different hospital)
            $table->string('referred_by_hospital_name')->nullable(); //doctor who refered the patient (from different hospital)

            // Snapshot of front desk user info
            $table->string('front_desk_user_name');
            $table->string('front_desk_user_email');
            $table->string('front_desk_user_phone')->nullable();

            $table->enum('status', array_column(StatusEnum::cases(), 'value'))->default('Pending');
            $table->enum('payment_status', array_column(PaymentStatusEnum::cases(), 'value'))->nullable();
            $table->dateTime('payment_date')->nullable();

            //medical_history
            $table->text("diet_plan")->nullable();
            $table->text("chief_complaints")->nullable();
            $table->text("surgical_history")->nullable();
            $table->text("co_morbidities")->nullable();
            $table->text("on_examination")->nullable();
            $table->text("treatment_plan")->nullable();
            $table->text("tests")->nullable();
            $table->text("additional_cost")->nullable();

            //estimated cost
            $table->decimal("consultation_amount", 10, 2)->nullable();
            $table->string("currency")->nullable();

            $table->timestamps();

            // Foreign key constraints without cascade deletes
            // $table->foreign('test_id')->references('id')->on('tests')->onDelete('set null');
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('set null');
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('front_desk_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
