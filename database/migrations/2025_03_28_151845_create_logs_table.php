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
        // Schema::dropIfExists('logs');

        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->text('subject');
            $table->string('status_type');
            $table->text('log')->nullable();
            $table->integer('status_code');
            $table->string('url');
            $table->string('method');
            $table->string('ip');
            $table->string('agent');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
