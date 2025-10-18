<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update any existing users with 'transformer' role to 'transporter'
        DB::table('users')
            ->where('role', 'transformer')
            ->update(['role' => 'transporter']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to 'transformer' if migration is rolled back
        DB::table('users')
            ->where('role', 'transporter')
            ->update(['role' => 'transformer']);
    }
};
