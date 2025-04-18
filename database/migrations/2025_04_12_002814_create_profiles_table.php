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
        Schema::create('profiles', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->unsignedBigInteger('id')->unique()->default(1);
            $table->string('address')->nullable();
            $table->enum('gender', ['L', 'P']);
            $table->timestamp('date_birth');
            $table->string('phone')->unique();
            $table->string('avatar')->nullable();

            $table->uuid('uuid_user');
            $table->foreign('uuid_user')->references('uuid')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
