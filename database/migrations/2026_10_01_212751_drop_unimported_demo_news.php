<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('news')->whereNull('imported_content_id')->delete();
    }

    public function down(): void
    {
        //
    }
};
