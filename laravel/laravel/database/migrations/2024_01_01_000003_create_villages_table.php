<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Desa di dalam satu kecamatan. `boundary` dipakai untuk menentukan desa
 * dari koordinat laporan (invariant #2: architecture.md) via ST_Contains.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('villages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('district_id');
            $table->string('name', 100);
            $table->string('code', 30)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();

            $table->foreign('district_id')
                ->references('id')->on('districts')
                ->restrictOnDelete();
        });

        DB::statement('ALTER TABLE villages ADD COLUMN boundary geometry(MultiPolygon, 4326) NOT NULL');
        DB::statement('CREATE INDEX idx_villages_boundary ON villages USING GIST (boundary)');
    }

    public function down(): void
    {
        Schema::dropIfExists('villages');
    }
};
