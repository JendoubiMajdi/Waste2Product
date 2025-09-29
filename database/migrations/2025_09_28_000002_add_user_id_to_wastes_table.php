<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wastes', function (Blueprint $table) {
            if (!Schema::hasColumn('wastes', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('localisation');
                $table->index('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('wastes', function (Blueprint $table) {
            if (Schema::hasColumn('wastes', 'user_id')) {
                // Drop index if exists then column
                try { $table->dropIndex(['user_id']); } catch (\Throwable $e) {}
                $table->dropColumn('user_id');
            }
        });
    }
};
