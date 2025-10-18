<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->text('delivery_address')->nullable()->after('statut');
            $table->unsignedBigInteger('transporter_id')->nullable()->after('delivery_address');
            $table->unsignedBigInteger('collection_point_id')->nullable()->after('transporter_id');
            $table->dateTime('estimated_delivery_time')->nullable()->after('collection_point_id');

            $table->foreign('transporter_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('collection_point_id')->references('id')->on('collection_points')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['transporter_id']);
            $table->dropForeign(['collection_point_id']);
            $table->dropColumn(['delivery_address', 'transporter_id', 'collection_point_id', 'estimated_delivery_time']);
        });
    }
};
