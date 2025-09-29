<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('wastes', function (Blueprint $table) {
            $table->bigInteger('collection_point_id')->unsigned()->nullable()->after('user_id');
            $table->foreign('collection_point_id')->references('id')->on('collection_points')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('wastes', function (Blueprint $table) {
            $table->dropForeign(['collection_point_id']);
            $table->dropColumn('collection_point_id');
        });
    }
};
