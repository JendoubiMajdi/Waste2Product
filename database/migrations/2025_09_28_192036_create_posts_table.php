<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('content')->nullable();
            $table->enum('media_type', ['text', 'image', 'video', 'link'])->nullable();
            $table->string('media_url')->nullable();
            $table->enum('post_type', ['normal', 'donation_share'])->default('normal');
            $table->foreignId('don_id')->nullable()->constrained('dons')->onDelete('set null');
            $table->integer('share_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};