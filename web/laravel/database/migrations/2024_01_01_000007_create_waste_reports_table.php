<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Entitas inti workflow. `status` adalah state machine laporan:
 *
 *   PENDING_VALIDATION -> VALIDATED | REJECTED
 *   VALIDATED -> ASSIGNED -> IN_PROGRESS -> PENDING_VERIFICATION
 *   PENDING_VERIFICATION -> RESOLVED | (kembali ke IN_PROGRESS via cleanup_tasks.VERIFICATION_REJECTED)
 *
 * Invariant #6 (architecture.md): RESOLVED tidak pernah dibuka kembali —
 * ditegakkan di application layer (service layer menolak transisi keluar
 * dari RESOLVED), bukan lewat DB constraint, karena Postgres CHECK tidak
 * bisa memvalidasi transisi state berdasarkan nilai sebelumnya.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waste_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->string('report_code', 50)->unique();
            $table->unsignedInteger('reported_by');
            $table->unsignedInteger('village_id');
            $table->unsignedInteger('category_id');
            $table->text('description')->nullable();
            $table->string('status', 50)->default('PENDING_VALIDATION');
            $table->unsignedInteger('validated_by')->nullable();
            $table->timestampTz('validated_at')->nullable();
            $table->timestampTz('resolved_at')->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();

            $table->foreign('reported_by')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('village_id')->references('id')->on('villages')->restrictOnDelete();
            $table->foreign('category_id')->references('id')->on('waste_categories')->restrictOnDelete();
            $table->foreign('validated_by')->references('id')->on('users')->nullOnDelete();

            $table->index('reported_by');
            $table->index('village_id');
            $table->index('category_id');
            $table->index('status');
        });

        DB::statement('ALTER TABLE waste_reports ADD COLUMN location geometry(Point, 4326) NOT NULL');
        DB::statement('CREATE INDEX idx_waste_reports_location ON waste_reports USING GIST (location)');

        DB::statement("ALTER TABLE waste_reports ADD CONSTRAINT chk_waste_reports_status CHECK (status IN (
            'PENDING_VALIDATION', 'VALIDATED', 'REJECTED',
            'ASSIGNED', 'IN_PROGRESS', 'PENDING_VERIFICATION', 'RESOLVED'
        ))");

        // Partial index: heatmap hanya butuh laporan aktif (bukan RESOLVED/REJECTED),
        // lebih hemat dan cepat dibanding full index untuk query peta.
        DB::statement("CREATE INDEX idx_waste_reports_active ON waste_reports (village_id)
            WHERE status NOT IN ('RESOLVED', 'REJECTED')");
    }

    public function down(): void
    {
        Schema::dropIfExists('waste_reports');
    }
};
