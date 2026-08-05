<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Enums\AppointmentTypeEnum;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('external_appointments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->integer('age')->nullable();
            $table->string('phone');
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->string('email');
            $table->string('citizenship')->nullable();
            $table->string('place_of_living')->nullable();
            $table->unsignedBigInteger('doctor_id');
            $table->dateTime('appointment_datetime');
            $table->date('alternate_date')->nullable();
            $table->string('appointment_type');
            $table->enum('visit_type', array_column(AppointmentTypeEnum::cases(), 'value'));
            $table->text('symptoms')->nullable();
            $table->enum('status', ['Pending', 'Confirmed','Payment Pending', 'Paid', 'Completed', 'Cancelled'])->default('Pending');
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('currency', 10)->default('₹')->nullable();
            $table->string('meeting_link')->nullable();
            $table->string('meeting_link_type')->default('manual');
            $table->enum('payment_type', ['link','Bank Transfer'])->nullable();
            $table->text('payment_info')->nullable();
            $table->string('transaction_id')->nullable();
            $table->dateTime('payment_date')->nullable();
            $table->string('payment_screenshot')->nullable();
            $table->string('appointment_reference_number', 50)->unique();
            $table->longText('daily_meeting_info')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_appointments');
    }
};
