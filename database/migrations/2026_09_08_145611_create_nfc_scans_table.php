<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nfc_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nfc_point_id')->constrained()->cascadeOnDelete();
            $table->foreignId('news_id')->nullable()->constrained('news')->nullOnDelete();
            $table->timestamp('scanned_at');
            $table->timestamps();

            $table->index(['nfc_point_id', 'scanned_at']);
            $table->index('scanned_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nfc_scans');
    }
};
