<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Satu tabel users untuk semua role (Super Admin Kecamatan, Admin Desa,
 * Petugas Desa, Masyarakat), dibedakan lewat role_id.
 *
 * `village_id` nullable karena Super Admin Kecamatan tidak terikat satu desa.
 * Aturan "wajib diisi untuk Admin Desa/Petugas" TIDAK ditegakkan di sini
 * (CHECK antar-kolom yang bergantung pada role_id tidak didukung native
 * oleh Postgres tanpa trigger) — harus divalidasi di FormRequest/service
 * layer Laravel saat user dibuat/diubah, sesuai code-standards.md.
 *
 * Auth mechanism (Sanctum/Passport/JWT) masih open decision di
 * architecture.md — kolom terkait token TIDAK ditambahkan di migration
 * ini agar tidak mendahului keputusan yang belum final.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('role_id');
            $table->unsignedInteger('village_id')->nullable();
            $table->string('name', 150);
            $table->string('email', 150)->unique();
            $table->string('password', 255);
            $table->string('phone', 30)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();

            $table->foreign('role_id')
                ->references('id')->on('roles')
                ->restrictOnDelete();
            $table->foreign('village_id')
                ->references('id')->on('villages')
                ->restrictOnDelete();

            $table->index('role_id');
            $table->index('village_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
