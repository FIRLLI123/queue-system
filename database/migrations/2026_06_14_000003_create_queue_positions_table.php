<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQueuePositionsTable extends Migration
{
    public function up()
    {
        Schema::create('queue_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('queue_number');
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->timestamps();
            $table->unique(['queue_number', 'status']);
            $table->unique(['user_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('queue_positions');
    }
}
