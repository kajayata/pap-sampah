<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Satu waste_report VALIDATED menghasilkan tepat satu cleanup_task
 * (invariant #3: architecture.md) — ditegakkan lewat UNIQUE pada report_id.
 *
 * `status` mencakup VERIFICATION_REJECTED agar invariant #5 terpenuhi:
 * Petugas tidak bisa langsung menetapkan RESOLVED — hasil pembersihan
 * harus lolos verifikasi Admin Desa, dan bila ditolak, task kembali
 * membutuhkan tindakan petugas (bukan otomatis RESOLVED).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cleanup_tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('report_id')->unique();
            $table->unsignedInteger('assigned_by');
            $table->string('status', 50)->default('ASSIGNED');
            $table->timestampTz('assigned_at')->useCurrent();
            $table->timestampTz('started_at')->nullable();
            $table->timestampTz('completed_at')->nullable();
            $table->text('notes')->nullable();

            $table->foreign('report_id')->references('id')->on('waste_reports')->restrictOnDelete();
            $table->foreign('assigned_by')->references('id')->on('users')->restrictOnDelete();
        });

        DB::statement("ALTER TABLE cleanup_tasks ADD CONSTRAINT chk_cleanup_tasks_status CHECK (status IN (
            'ASSIGNED', 'IN_PROGRESS', 'PENDING_VERIFICATION', 'VERIFICATION_REJECTED', 'COMPLETED'
        ))");
    }

    public function down(): void
    {
        Schema::dropIfExists('cleanup_tasks');
    }
};
