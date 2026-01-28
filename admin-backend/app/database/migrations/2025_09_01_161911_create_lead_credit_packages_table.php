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
        Schema::create('lead_credit_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();                      // Example: "Starter Pack"
            $table->integer('credits');                  // Example: 50 credits
            $table->decimal('price', 10, 2);             // Example: 500.00
            $table->string('currency', 10)->default('BDT');
            $table->integer('duration_months')->default(3); // Expiry duration
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_credit_packages');
    }
};
