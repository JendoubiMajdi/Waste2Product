<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL, use native ENUM modification
        // For SQLite, this is handled by the original migration's string type
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE challenges MODIFY COLUMN status ENUM('upcoming', 'active', 'inactive', 'completed') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE challenges MODIFY COLUMN status ENUM('upcoming', 'active', 'completed') NOT NULL");
        }
    }
};
