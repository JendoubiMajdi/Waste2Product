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
        Schema::table('events', function (Blueprint $table) {
            $table->text('ai_sentiment_summary')->nullable()->after('status');
            $table->decimal('ai_sentiment_score', 3, 2)->nullable()->after('ai_sentiment_summary'); // 0.00 to 1.00
            $table->json('ai_insights')->nullable()->after('ai_sentiment_score');
            $table->timestamp('ai_analyzed_at')->nullable()->after('ai_insights');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['ai_sentiment_summary', 'ai_sentiment_score', 'ai_insights', 'ai_analyzed_at']);
        });
    }
};
