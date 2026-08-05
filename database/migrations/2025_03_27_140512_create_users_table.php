<?php

use App\Enums\GenderEnum;
use App\Enums\MaritalStatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            // $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('image')->nullable();
            $table->string('id_proof_for_pan')->nullable();
            $table->string('role');
            $table->text('letter_header_info')->nullable();
            $table->string('department_code')->nullable();
            $table->string('department_name');
            $table->unsignedBigInteger('system_settings_id')->nullable();
            $table->date('DOB')->nullable();
            $table->integer('age')->nullable();
            $table->enum('gender', array_column(GenderEnum::cases(), 'value'))->nullable();
            $table->string('password');
            $table->text('address')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('pincode', 10)->nullable();
            $table->enum('marital_status', array_column(MaritalStatusEnum::cases(), 'value'))->nullable();
            $table->string('designation')->nullable();
            $table->string('qualification')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->string('department');
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('status', [
                'Active',
                'Inactive',
            ])->nullable();
            $table->longText('available_days')->nullable();
            $table->string('slot_duration', 50)->nullable();
            $table->text('leave_date')->nullable();
            // $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('system_settings_id')->references('id')->on('system_settings')->onDelete('cascade');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
