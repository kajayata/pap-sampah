<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Metadata foto laporan — binary aktual berada di object/file storage
 * (invariant #9: architecture.md). `report_id` sengaja RESTRICT (bukan
 * CASCADE) agar penghapusan waste_reports tidak diam-diam menghapus foto
 * (invariant #8: "hilangnya mark dari peta tidak berarti penghapusan foto").
 *
 * Aturan "minimal 1 foto per laporan" DITEGAKKAN DI APPLICATION LAYER:
 * FormRequest menolak submit tanpa foto, dan insert waste_reports +
 * waste_report_photos dibungkus dalam satu DB transaction.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waste_report_photos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('report_id');
            $table->string('storage_key', 500)->unique();
            $table->string('mime_type', 100);
            $table->unsignedInteger('file_size');
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
            $table->timestampTz('captured_at')->nullable();
            $table->unsignedInteger('uploaded_by');
            $table->timestampTz('created_at')->useCurrent();

            $table->foreign('report_id')->references('id')->on('waste_reports')->restrictOnDelete();
            $table->foreign('uploaded_by')->references('id')->on('users')->restrictOnDelete();

            $table->index('report_id');
        });

        DB::statement('ALTER TABLE waste_report_photos ADD COLUMN location geometry(Point, 4326) NULL');
        DB::statement('CREATE INDEX idx_waste_report_photos_location ON waste_report_photos USING GIST (location) WHERE location IS NOT NULL');
    }

    public function down(): void
    {
        Schema::dropIfExists('waste_report_photos');
    }
};
