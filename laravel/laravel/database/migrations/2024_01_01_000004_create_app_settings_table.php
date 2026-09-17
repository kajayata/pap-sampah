<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menyimpan nilai konfigurasi yang bisa berubah tanpa deploy ulang,
 * mis. `marker_display_days` (default H+7 pada mark selesai — lihat
 * code-standards.md: "H+7 hanya merupakan aturan tampilan marker default").
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->string('key', 100)->primary();
            $table->text('value');
            $table->text('description')->nullable();
            $table->timestampTz('updated_at')->useCurrent();
        });

        // Seed nilai default H+7 sesuai project-overview.md.
        \DB::table('app_settings')->insert([
            'key' => 'marker_display_days',
            'value' => '7',
            'description' => 'Jumlah hari mark selesai tetap tampil di peta setelah resolved_at',
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
