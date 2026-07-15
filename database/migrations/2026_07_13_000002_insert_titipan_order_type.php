<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\OrderType;

class InsertTitipanOrderType extends Migration
{
    public function up()
    {
        // Insert TITIPAN order type as ACTIVE
        OrderType::firstOrCreate(
            ['name' => 'TITIPAN'],
            ['status' => 'ACTIVE']
        );
    }

    public function down()
    {
        OrderType::where('name', 'TITIPAN')->delete();
    }
}
