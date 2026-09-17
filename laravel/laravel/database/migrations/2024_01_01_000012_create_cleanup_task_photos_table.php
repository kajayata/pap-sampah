<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Foto before/after hasil pembersihan yang dikirim petugas dan diverifikasi
 * Admin Desa. `task_id` RESTRICT (bukan CASCADE) — foto adalah bukti hasil
 * kerja dan tidak boleh hilang diam-diam (invariant #8: architecture.md).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cleanup_task_photos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('task_id');
            $table->string('type', 30);
            $table->string('storage_key', 500)->unique();
            $table->string('mime_type', 100);
            $table->unsignedInteger('file_size');
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
            $table->timestampTz('captured_at')->nullable();
            $table->unsignedInteger('uploaded_by');
            $table->timestampTz('created_at')->useCurrent();

            $table->foreign('task_id')->references('id')->on('cleanup_tasks')->restrictOnDelete();
            $table->foreign('uploaded_by')->references('id')->on('users')->restrictOnDelete();

            $table->index('task_id');
        });

        DB::statement("ALTER TABLE cleanup_task_photos ADD CONSTRAINT chk_cleanup_task_photos_type CHECK (type IN ('BEFORE', 'AFTER'))");

        DB::statement('ALTER TABLE cleanup_task_photos ADD COLUMN location geometry(Point, 4326) NULL');
        DB::statement('CREATE INDEX idx_cleanup_task_photos_location ON cleanup_task_photos USING GIST (location) WHERE location IS NOT NULL');
    }

    public function down(): void
    {
        Schema::dropIfExists('cleanup_task_photos');
    }
};
