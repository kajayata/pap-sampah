<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Audit trail setiap perubahan status laporan. `report_id` RESTRICT
 * (bukan CASCADE) — histori tidak boleh hilang meski report dianggap
 * "selesai secara visual" (invariant #8: architecture.md).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waste_report_status_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('report_id');
            $table->string('status', 50);
            $table->unsignedInteger('changed_by');
            $table->text('note')->nullable();
            $table->timestampTz('created_at')->useCurrent();

            $table->foreign('report_id')->references('id')->on('waste_reports')->restrictOnDelete();
            $table->foreign('changed_by')->references('id')->on('users')->restrictOnDelete();

            $table->index('report_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waste_report_status_histories');
    }
};
