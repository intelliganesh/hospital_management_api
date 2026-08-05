<?php

use App\Enums\BloodGroupEnum;
use App\Enums\GenderEnum;
use App\Enums\MaritalStatusEnum;
use App\Enums\PatientStatusEnum;
use App\Enums\Payment\AmountForEnum;
use App\Enums\DietaryPreferenceEnum;
// use App\Enums\Payment\PaymentTypeEnum;
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
        Schema::create('patients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique()->nullable();
            $table->string('patient_number')->unique();
            $table->string('opd_number')->unique()->nullable();
            // $table->float('enroll_fees');
            // $table->enum('payment_type', array_column(PaymentTypeEnum::cases(), 'value'))->nullable();
            // $table->string('password');
            $table->string('phone_no')->nullable();
            $table->date('dob')->nullable();
            $table->integer('age')->unsigned()->nullable();
            $table->enum('gender', array_column(GenderEnum::cases(), 'value'))->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('place_of_living')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->enum('dietary_preference', array_column(DietaryPreferenceEnum::cases(), 'value'))->nullable();
            // $table->enum('dietary_preference', ['Vegetarian', 'Non Vegetarian', 'Vegan', 'Eggtarian'])->nullable();
            $table->string('pincode', 10)->nullable();
            $table->enum('marital_status', array_column(MaritalStatusEnum::cases(), 'value'))->nullable();
            $table->enum('blood_group', array_column(BloodGroupEnum::cases(), 'value'))->nullable();
            $table->string('insurance_provider')->nullable();
            $table->string('insurance_policy_no')->nullable();
            $table->string('attendant_with_patient_name')->nullable();
            $table->string('attendant_with_patient_phone_no')->nullable();
                                                                          // $table->unsignedBigInteger('referred_to')->nullable(); //doctor name
            $table->unsignedBigInteger('front_desk_user_id')->nullable(); //front desk user
            $table->string('referred_by_name')->nullable();               //doctor who refered the patient (from different hospital)
                                                                          // $table->string('referred_by_phone_no')->nullable(); //doctor who refered the patient (from different hospital)
                                                                          // $table->string('referred_by_email')->nullable(); //doctor who refered the patient (from different hospital)
                                                                          // $table->string('referred_by_hospital_name')->nullable(); //doctor who refered the patient (from different hospital)
            $table->enum('amount_for', array_column(AmountForEnum::cases(), 'value'));
            // $table->enum('admission_status', ['Admission Pending', 'Admitted', 'Discharge Pending', 'Discharged', 'Closed'])->nullable();
            // $table->enum('treatment_status', ['Under Diagnosis', 'Test Pending', 'Test Completed', 'Prescribed', 'In Treatment', 'Under Observation', 'Follow-up Required'])->default('Test Pending');
            // $table->enum('surgery_status', ['Surgery Scheduled', 'Surgery In Progress', 'Surgery Completed'])->nullable();
            // $table->enum('emergency_status', ['Emergency', 'Critical', 'Stable', 'Deceased'])->nullable();
            // $table->enum('referral_status', ['Not Referred', 'Referred', 'Transferred'])->default('Not Referred');
            // $table->enum('payment_status', ['Payment Pending', 'Payment Completed'])->default('Payment Pending');
            $table->enum('status', array_column(PatientStatusEnum::cases(), 'value'))->default(PatientStatusEnum::Active->value);
            $table->timestamps();

            // Foreign key constraints without cascading deletes
            // $table->foreign('referred_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('front_desk_user_id')->references('id')->on('users')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
