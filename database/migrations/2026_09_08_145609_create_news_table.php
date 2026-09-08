<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('imported_content_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('source_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary')->nullable();
            $table->longText('body');
            $table->string('featured_image_path')->nullable();
            $table->json('gallery')->nullable();
            $table->string('origin_url')->nullable();
            $table->timestamp('origin_published_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('status');
            $table->json('processed_payload')->nullable();
            $table->timestamp('admin_edited_at')->nullable();
            $table->timestamp('event_starts_at')->nullable();
            $table->timestamp('event_ends_at')->nullable();
            $table->timestamps();

            $table->unique('imported_content_id');
            $table->index('status');
            $table->index('published_at');
        });

        Schema::create('news_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained('news')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['news_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_tag');
        Schema::dropIfExists('news');
    }
};
