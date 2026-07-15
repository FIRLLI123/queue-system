<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('titipan_requirements', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Seed default requirements
        DB::table('titipan_requirements')->insert([
            ['name' => 'Pilihan 1', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pilihan 2', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('titipan_requirements');
    }
};
