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
        Schema::create('user_has_roles', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->unsignedBigInteger('id')->unique()->default(1);
            
            $table->uuid('uuid_user');
            $table->foreign('uuid_user')->references('uuid')->on('users')->onDelete('cascade');

            $table->uuid('uuid_role');
            $table->foreign('uuid_role')->references('uuid')->on('roles')->onDelete('cascade');

            $table->unique(['uuid_user', 'uuid_role']);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_has_roles');
    }
};
