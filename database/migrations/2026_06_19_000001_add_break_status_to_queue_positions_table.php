<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddBreakStatusToQueuePositionsTable extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE queue_positions MODIFY status ENUM('ACTIVE', 'INACTIVE', 'BREAK') NOT NULL DEFAULT 'ACTIVE'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE queue_positions MODIFY status ENUM('ACTIVE', 'INACTIVE') NOT NULL DEFAULT 'ACTIVE'");
    }
}
