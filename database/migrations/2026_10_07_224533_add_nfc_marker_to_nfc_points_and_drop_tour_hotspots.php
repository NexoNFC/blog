<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('tour_hotspots');

        if (! Schema::hasColumn('nfc_points', 'nfc_marker_theta')) {
            Schema::table('nfc_points', function (Blueprint $table) {
                $table->float('nfc_marker_theta')->nullable()->after('tour_description');
            });
        }

        if (! Schema::hasColumn('nfc_points', 'nfc_marker_phi')) {
            Schema::table('nfc_points', function (Blueprint $table) {
                $table->float('nfc_marker_phi')->nullable()->after('nfc_marker_theta');
            });
        }
    }

    public function down(): void
    {
        Schema::table('nfc_points', function (Blueprint $table) {
            $columns = array_values(array_filter([
                Schema::hasColumn('nfc_points', 'nfc_marker_theta') ? 'nfc_marker_theta' : null,
                Schema::hasColumn('nfc_points', 'nfc_marker_phi') ? 'nfc_marker_phi' : null,
            ]));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
