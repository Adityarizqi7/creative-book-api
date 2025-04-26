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
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->unsignedBigInteger('id')->unique()->default(1);

            $table->uuid('uuid_user');
            $table->uuid('uuid_store');
            $table->uuid('uuid_book');
            $table->integer('quantity');
            $table->integer('final_price');
            $table->integer('total_price');
            $table->integer('service_fee')->nullable();
            $table->string('order_id');
            $table->string('snap_token');
            $table->string('snap_url');
            $table->enum('status', ['pending', 'paid', 'failed', 'expired'])->default('pending');

            $table->foreign('uuid_user')->references('uuid')->on('users')->onDelete('cascade');
            $table->foreign('uuid_store')->references('uuid')->on('stores')->onDelete('cascade');
            $table->foreign('uuid_book')->references('uuid')->on('books')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
