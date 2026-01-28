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
        Schema::create('business_details', function (Blueprint $table) {
            $table->id();
            $table->integer('established')->max(4)->min(4)->nullable();
            $table->string('number_of_employee')->nullable();
            $table->text('about')->nullable();
            $table->text('direction')->nullable();
            $table->text('payment_type')->nullable();
            $table->integer('tin')->max(12)->min(12)->nullable();
            $table->string('website')->nullable();
            $table->string('facebook')->nullable();
            $table->string('video_url')->nullable();
            $table->jsonb('timing')->nullable();
            $table->unsignedBigInteger('business_id');
            $table->foreign('business_id')->references('id')->on('businesses')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_details');
    }
};
