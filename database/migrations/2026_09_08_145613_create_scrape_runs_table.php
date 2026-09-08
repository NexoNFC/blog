<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scrape_runs', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->string('trigger')->default('scheduled');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->unsignedInteger('contents_found')->default(0);
            $table->unsignedInteger('contents_new')->default(0);
            $table->unsignedInteger('contents_processed')->default(0);
            $table->unsignedInteger('news_created')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('started_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scrape_runs');
    }
};
