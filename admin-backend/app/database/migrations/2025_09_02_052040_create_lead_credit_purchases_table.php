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
        Schema::create('lead_credit_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->foreignId('package_id')->constrained('lead_credit_packages');
            $table->integer('credits');
            $table->decimal('amount', 10, 2); // BDT
            $table->string('payment_method')->nullable(); // bKash, Nagad, Stripe, etc.
            $table->string('transaction_id')->nullable();
            $table->enum('status', ['pending','completed','failed'])->default('completed');
            $table->date('expire_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_credit_purchases');
    }
};
