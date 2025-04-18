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
        Schema::create('book_stores', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->unsignedBigInteger('id')->unique()->default(1);
            $table->uuid('uuid_promo')->nullable();
            $table->decimal('original_price', 12, 2);
            $table->decimal('final_price', 12, 2);
            
            $table->uuid('uuid_book');
            $table->foreign('uuid_book')->references('uuid')->on('books')->onDelete('cascade');

            $table->uuid('uuid_store');
            $table->foreign('uuid_store')->references('uuid')->on('stores')->onDelete('cascade');

            $table->unique(['uuid_book', 'uuid_store']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_stores');
    }
};
