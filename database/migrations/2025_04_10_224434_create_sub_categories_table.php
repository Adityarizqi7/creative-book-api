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
        Schema::create('sub_categories', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->unsignedBigInteger('id')->unique()->default(1);
            $table->string('title');
            $table->string('slug')->unique();
            $table->timestamps();

            $table->uuid('uuid_category')->nullable();
            $table->foreign('uuid_category')->references('uuid')->on('categories');
            $table->uuid('uuid_parent_sub_category')->nullable();
            $table->foreign('uuid_parent_sub_category')->references('uuid')->on('sub_categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_categories');
    }
};
