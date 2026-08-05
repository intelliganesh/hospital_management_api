<?php

use App\Enums\DoctorStatusEnum;
use App\Enums\GenderEnum;
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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('doctor_code')->unique();
            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->enum('gender', array_column(GenderEnum::cases(), "value"));
            $table->date('dob')->nullable();
            $table->string('qualification')->nullable();
            $table->string('specialization')->nullable();
            $table->string('department_id')->nullable();
            // $table->unsignedBigInteger('department_id')->nullable();
            $table->integer('experience_years')->nullable();
            $table->string('registration_no')->nullable();
            $table->enum('consulting_type', ['Consulting', 'Attending', 'Both'])->default('Consulting');
            $table->json('availability_days')->nullable();
            $table->string('available_timings')->nullable();
            $table->string('photo')->nullable();
            $table->text('address')->nullable();
            $table->enum('status', array_column(DoctorStatusEnum::cases(), 'value'))->default('Active');
            $table->timestamps();

            // $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
