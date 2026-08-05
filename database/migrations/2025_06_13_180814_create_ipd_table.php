<?php

use App\Enums\GenderEnum;
use App\Enums\RelationEnum;
use App\Enums\BloodGroupEnum;
use App\Enums\MaritalStatusEnum;
use App\Enums\ReferralSourceEnum;
use App\Enums\AddmissionTypeEnum;
use App\Enums\DietaryPreferenceEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
// use App\Enums\Payment\PaymentTypeEnum;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ipd', function (Blueprint $table) {
            $table->uuid('id')->primary();

            /* IPD identification */
            $table->string('ipd_number')->unique();

            /* patient information */
            $table->uuid('patient_id')->nullable();
            $table->string('patient_number',10);
            $table->string('patient_name');
            $table->string('patient_email')->nullable();
            $table->string('patient_phone')->nullable();
            $table->integer('patient_age')->unsigned()->nullable();
            $table->string('patient_attendant_name')->nullable();
            $table->string('patient_attendant_phone')->nullable();
            $table->text('patient_address')->nullable();

            /* consultation and admission */
            $table->uuid('consultation_id')->nullable();
            $table->dateTime('admission_date_time')->nullable();
            $table->dateTime('discharge_date_time')->nullable();

            /* ward information */
            $table->unsignedBigInteger('ward_id')->nullable();
            $table->string('ward_number')->nullable();
            $table->string('ward_type')->nullable();

            /* room information */
            $table->unsignedBigInteger('room_id')->nullable();
            $table->string('room_type')->nullable();
            $table->string('room_number')->nullable();

            /* bed information */
            $table->string('bed_number')->nullable();

            $table->enum('status', ['Admitted', 'Discharged', 'Expired', 'Under Treatment'])->default('Admitted');

            $table->timestamps();

            /* Foreign key constraints */
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('set null');
            $table->foreign('ward_id')->references('id')->on('ward')->onDelete('set null');
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ipd');
    }
};