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
        Schema::create('promos', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->unsignedBigInteger('id')->unique()->default(1);
            $table->bigInteger('discount');
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamp('start_date_flash_sale');
            $table->timestamp('end_date_flash_sale');
            $table->timestamps();

            $table->uuid('uuid_store')->nullable();
            $table->foreign('uuid_store')->references('uuid')->on('stores');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
