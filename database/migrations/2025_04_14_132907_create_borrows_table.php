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
        Schema::create('borrows', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->unsignedBigInteger('id')->unique()->default(1);
            $table->date('borrowed_at');
            $table->date('returned_at')->nullable();
            $table->date('due_at')->nullable();
            $table->enum('status', ['borrowed', 'returned', 'late'])->default('borrowed');
            $table->decimal('fine', 8, 2)->default(0);
            
            $table->uuid('uuid_user');
            $table->foreign('uuid_user')->references('uuid')->on('users')->onDelete('cascade');
            
            $table->uuid('uuid_book');
            $table->foreign('uuid_book')->references('uuid')->on('books')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrows');
    }
};
