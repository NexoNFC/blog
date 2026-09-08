<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imported_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')->constrained()->cascadeOnDelete();
            $table->string('external_id')->nullable();
            $table->string('origin_url');
            $table->timestamp('origin_published_at')->nullable();
            $table->timestamp('extracted_at');
            $table->string('content_hash')->nullable();
            $table->string('title')->nullable();
            $table->longText('raw_text')->nullable();
            $table->longText('raw_html')->nullable();
            $table->json('media')->nullable();
            $table->json('metadata')->nullable();
            $table->string('status');
            $table->text('processing_error')->nullable();
            $table->timestamps();

            $table->unique(['source_id', 'origin_url']);
            $table->index(['source_id', 'external_id']);
            $table->index('status');
            $table->index('content_hash');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imported_contents');
    }
};
