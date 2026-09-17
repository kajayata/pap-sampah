<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Satu cleanup_task bisa punya banyak petugas (invariant #3: architecture.md);
 * setiap petugas punya status penerimaan sendiri. `worker_id` yang beroperasi
 * di luar desanya (invariant #4) TIDAK dicegah lewat FK — harus divalidasi
 * di service layer saat assignment dibuat (bandingkan worker.village_id
 * dengan report.village_id).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cleanup_task_workers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('task_id');
            $table->unsignedInteger('worker_id');
            $table->string('status', 30)->default('PENDING');
            $table->text('rejection_reason')->nullable();
            $table->timestampTz('assigned_at')->useCurrent();
            $table->timestampTz('accepted_at')->nullable();
            $table->timestampTz('started_at')->nullable();
            $table->timestampTz('completed_at')->nullable();

            $table->foreign('task_id')->references('id')->on('cleanup_tasks')->cascadeOnDelete();
            $table->foreign('worker_id')->references('id')->on('users')->restrictOnDelete();

            $table->unique(['task_id', 'worker_id']);
        });

        DB::statement("ALTER TABLE cleanup_task_workers ADD CONSTRAINT chk_cleanup_task_workers_status CHECK (status IN (
            'PENDING', 'ACCEPTED', 'REJECTED', 'COMPLETED'
        ))");

        // Alasan penolakan wajib diisi ketika status REJECTED.
        DB::statement("ALTER TABLE cleanup_task_workers ADD CONSTRAINT chk_rejection_reason CHECK (
            status <> 'REJECTED' OR rejection_reason IS NOT NULL
        )");
    }

    public function down(): void
    {
        Schema::dropIfExists('cleanup_task_workers');
    }
};
