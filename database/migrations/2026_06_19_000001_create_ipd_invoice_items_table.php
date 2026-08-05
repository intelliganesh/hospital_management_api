<?php

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
        Schema::create('ipd_invoice_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ipd_id');
            $table->uuid('invoice_id');
            $table->float('amount')->default(0);
            $table->unsignedBigInteger('front_desk_user_id')->nullable();
            $table->string('front_desk_user_name')->nullable();
            $table->string('front_desk_user_email')->nullable();
            $table->string('front_desk_user_phone')->nullable();
            $table->string('service_category')->nullable();
            $table->string('currency', 10)->nullable();
            $table->text('description')->nullable();
            $table->float('tax_percent')->default(0);
            $table->float('tax_amount')->default(0);
            $table->date('service_date')->nullable();
            $table->timestamps();

            $table->foreign('ipd_id')->references('id')->on('ipd')->onDelete('cascade');
            $table->foreign('invoice_id')->references('id')->on('invoice')->onDelete('cascade');
            $table->foreign('front_desk_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ipd_invoice_items');
    }
};
