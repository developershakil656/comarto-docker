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
        Schema::create('review_galleries', function (Blueprint $table) {
            $table->id();
            $table->text('url');
            $table->unsignedBigInteger('business_review_id');
            $table->enum('status',['active','blocked'])->default('active');
            $table->foreign('business_review_id')->references('id')->on('business_reviews')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_galleries');
    }
};
