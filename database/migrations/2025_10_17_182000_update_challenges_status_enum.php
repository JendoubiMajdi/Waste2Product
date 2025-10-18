<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change the enum to allow 'active' and 'inactive'
        DB::statement("ALTER TABLE challenges MODIFY COLUMN status ENUM('upcoming', 'active', 'inactive', 'completed') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE challenges MODIFY COLUMN status ENUM('upcoming', 'active', 'completed') NOT NULL");
    }
};
