<?php

use Illuminate\Database\Migrations\Migration;

/**
 * Legacy placeholder: the tour no longer uses linked hotspots.
 * Marker fields live on nfc_points (see later migrations).
 */
return new class extends Migration
{
    public function up(): void
    {
        //
    }

    public function down(): void
    {
        //
    }
};
