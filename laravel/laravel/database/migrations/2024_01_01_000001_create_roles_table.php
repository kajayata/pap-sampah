<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Master data role sistem: Super Admin Kecamatan, Admin Desa, Petugas Desa, Masyarakat.
 * PK bertipe INT (bukan BIGINT) sesuai code-standards.md — tabel referensi kecil,
 * tidak ada kebutuhan skala yang membenarkan BIGINT.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
