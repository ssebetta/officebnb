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
        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['basic', 'premium'])->default('basic');
            $table->string('image_url');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['pending_payment', 'active', 'expired', 'cancelled'])->default('pending_payment');
            $table->integer('amount_cents')->comment('Amount in cents');
            $table->string('payment_method')->nullable()->comment('stripe or mobile_money');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advertisements');
    }
};
