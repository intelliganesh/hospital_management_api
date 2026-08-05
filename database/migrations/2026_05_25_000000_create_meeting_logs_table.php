<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meeting_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('external_appointment_id');
            $table->string('room_name');
            $table->string('meeting_status');
            $table->timestamps();

            $table->foreign('external_appointment_id')
                ->references('id')
                ->on('external_appointments')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meeting_logs');
    }
};
