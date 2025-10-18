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
        Schema::table('challenges', function (Blueprint $table) {
            $table->date('start_date')->nullable()->change();
            $table->date('end_date')->nullable()->change();
            $table->string('goal')->nullable()->change();
        });

        // Update status enum to include 'inactive' (MySQL only)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE challenges MODIFY COLUMN status ENUM('upcoming', 'active', 'inactive', 'completed') NOT NULL DEFAULT 'active'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('challenges', function (Blueprint $table) {
            $table->date('start_date')->nullable(false)->change();
            $table->date('end_date')->nullable(false)->change();
            $table->string('goal')->nullable(false)->change();
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE challenges MODIFY COLUMN status ENUM('upcoming', 'active', 'completed') NOT NULL");
        }
    }
};
