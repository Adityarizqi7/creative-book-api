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
        Schema::create('books', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->unsignedBigInteger('id')->unique()->default(1);
            $table->string('name')->unique();
            $table->longText('description');
            $table->string('slug')->unique();
            $table->string('image');
            $table->string('variant_code');
            $table->string('variant_name');
            $table->timestamp('date_publish');
            $table->unsignedBigInteger('page');
            $table->string('ISBN');
            $table->string('language');
            $table->float('long');
            $table->float('weight');
            $table->float('width');

            $table->uuid('uuid_sub_category')->nullable();
            $table->foreign('uuid_sub_category')->references('uuid')->on('sub_categories');

            $table->uuid('uuid_writer')->nullable();
            $table->foreign('uuid_writer')->references('uuid')->on('writers');

            $table->uuid('uuid_publisher')->nullable();
            $table->foreign('uuid_publisher')->references('uuid')->on('publishers');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
