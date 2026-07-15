<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTitipanOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('titipan_orders', function (Blueprint $table) {
            $table->id();
            $table->date('booking_date');
            $table->time('booking_time');
            $table->string('requirement');
            $table->text('description')->nullable();
            $table->string('status')->default('CREATE'); // 'CREATE' or 'COMPLETED'
            $table->foreignId('taken_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('taken_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('titipan_orders');
    }
}
