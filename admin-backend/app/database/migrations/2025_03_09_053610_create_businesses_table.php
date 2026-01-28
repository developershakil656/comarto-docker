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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('business_name');
            $table->string('slug')->unique();
            $table->string('business_profile')->nullable();
            $table->string('number');
            $table->string('alternate_number')->nullable();
            $table->string('email')->nullable();
            $table->text('address');
            $table->integer('post_code')->max(4)->min(4);
            $table->unsignedBigInteger('location_id');
            $table->foreign('location_id')->references('id')->on('locations');
            // $table->enum('business_type',['manufacturers','distributors','exporters','importers','wholesalers']);
            $table->json('business_type');
            $table->enum('status',['active','inactive','blocked'])->default('active');
            $table->unsignedBigInteger('user_id')->unique();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
