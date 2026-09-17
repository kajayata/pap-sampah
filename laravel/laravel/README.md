# 🌿 Pap Sampah — Backend & Web Administration (Laravel)

Modul ini adalah backend REST API dan Web Administration dari platform **Pap Sampah (Kecamatan Sumbersari)** yang dibangun dengan Laravel 12, PostgreSQL + PostGIS, dan Blade + Leaflet.

> 📖 **PANDUAN LENGKAP SETUP & KOLABORASI:**  
> Silakan lihat panduan lengkap di [README.md Root Repositori](../../README.md).

---

## ⚡ Quick Backend Setup

1. **Jalankan Database Docker (dari root project):**
   ```bash
   cd ../.. && docker compose up -d && cd laravel/laravel
   ```
2. **Install Dependencies & Environment:**
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   php artisan storage:link
   ```
3. **Database Migration & Seeders:**
   ```bash
   php artisan migrate:fresh --seed
   ```
4. **Verifikasi Test Suite (22 Tests):**
   ```bash
   php artisan test
   ```
5. **Jalankan Development Server:**
   ```bash
   php artisan serve
   ```
   Akses Web Admin: `http://127.0.0.1:8000/login`

---

## 👥 Demo Akun Login

Password untuk semua akun: `password123`
- **Super Admin Kecamatan:** `kec.sumbersari@papsampah.id`
- **Admin Kelurahan Sumbersari:** `kel.sumbersari@papsampah.id`
- *(Admin kelurahan lain: `kel.[nama_kelurahan]@papsampah.id`)*
