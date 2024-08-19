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
        Schema::create('session_carts', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique();
            $table->timestamps();
        });

        Schema::create('session_cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_cart_id')->constrained('session_carts');
            $table->foreignId('product_id')->constrained();
            $table->integer('quantity');
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_carts');
    }
};
