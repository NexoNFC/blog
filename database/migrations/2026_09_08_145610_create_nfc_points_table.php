<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nfc_points', function (Blueprint $table) {
            $table->id();
            $table->string('identifier')->unique();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('location');
            $table->text('description')->nullable();
            $table->string('status');
            $table->string('kind')->nullable();
            $table->string('image_path')->nullable();
            $table->foreignId('news_id')->nullable()->constrained('news')->nullOnDelete();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nfc_points');
    }
};
