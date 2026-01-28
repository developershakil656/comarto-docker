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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('details');
            $table->string('price');
            $table->integer('moq')->nullable();
            $table->string('video_url')->nullable();
            $table->json('specification')->nullable();
            // $table->json('category_ids');
            $table->enum('stock',['in stock','out of stock'])->default('in stock');
            $table->enum('status',['active','inactive','blocked'])->default('active');
            $table->integer('unit_quantity');
            $table->unsignedBigInteger('product_unit_id')->default(1);
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
        Schema::dropIfExists('products');
    }
};
