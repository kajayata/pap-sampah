<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Kecamatan — satu-satunya scope wilayah sistem (invariant #1: architecture.md).
 * Kolom `boundary` memakai raw SQL karena Laravel Schema Builder tidak punya
 * tipe geometry PostGIS bawaan. Pendekatan ini dipilih (bukan menambah package
 * spatial pihak ketiga) agar dependency tetap minimal — konsisten dengan
 * code-standards.md yang tidak menyebutkan package spatial sebagai keputusan final.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('code', 30)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();
        });

        // SRID 4326 (WGS84) agar konsisten dengan koordinat GPS dari Flutter.
        DB::statement('ALTER TABLE districts ADD COLUMN boundary geometry(MultiPolygon, 4326) NOT NULL');
        DB::statement('CREATE INDEX idx_districts_boundary ON districts USING GIST (boundary)');
    }

    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};
