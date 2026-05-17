<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->string('action'); // Describe the action (e.g., "User created", "Product updated")
            $table->text('details')->nullable(); // Optional details about the action
            $table->unsignedBigInteger('user_id')->nullable(); // ID of the user who performed the action
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('logs');
    }
}
