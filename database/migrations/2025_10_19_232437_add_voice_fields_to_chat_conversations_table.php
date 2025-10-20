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
        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->string('voice_file_path')->nullable()->after('response');
            $table->text('voice_transcription')->nullable()->after('voice_file_path');
            $table->string('voice_tone')->nullable()->after('voice_transcription'); // angry, happy, sad, neutral, excited, frustrated, etc.
            $table->integer('voice_duration')->nullable()->after('voice_tone'); // in seconds
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_conversations', function (Blueprint $table) {
            $table->dropColumn(['voice_file_path', 'voice_transcription', 'voice_tone', 'voice_duration']);
        });
    }
};
