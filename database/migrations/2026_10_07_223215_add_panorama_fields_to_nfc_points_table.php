<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nfc_points', function (Blueprint $table) {
            $table->string('panorama_path')->nullable()->after('image_path');
            $table->boolean('tour_enabled')->default(false)->after('panorama_path');
            $table->text('tour_description')->nullable()->after('tour_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('nfc_points', function (Blueprint $table) {
            $table->dropColumn(['panorama_path', 'tour_enabled', 'tour_description']);
        });
    }
};
