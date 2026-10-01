<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE waste_banks ALTER COLUMN phone TYPE BIGINT USING NULLIF(phone::text, \'\')::BIGINT');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE waste_banks ALTER COLUMN phone TYPE VARCHAR(13) USING phone::text');
    }
};