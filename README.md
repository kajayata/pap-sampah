# 🌿 Pap Sampah — Sistem Informasi & Pengelolaan Sampah Terpadu
> **Wilayah Fokus:** Kecamatan Sumbersari, Kabupaten Jember (7 Kelurahan: Antirogo, Karangrejo, Kebonsari, Kranjingan, Tegalgede, Wirolegi, Sumbersari).

Selamat datang di repositori monorepo **Pap Sampah**. Dokumen ini dibuat agar seluruh anggota tim developer (Backend, Web, dan Mobile Flutter) dapat melakukan **clone, setup lingkungan lokal, dan berkolaborasi tanpa kendala error**.

---

## 📁 Struktur Monorepo

```text
project-3-polije/
├── docker-compose.yml       # Container PostgreSQL 17 + PostGIS 3.5 Spasial
├── laravel/
│   └── laravel/             # Backend API (Laravel 12, Sanctum, Web Admin Blade + Leaflet)
├── mobile-app/
│   └── mobile/              # Aplikasi Mobile Warga & Petugas (Flutter SDK)
├── docs/                    # Dokumentasi arsitektur, flow bisnis, & konteks teknis
└── README.md                # Panduan kolaborasi developer (file ini)
```

---

## 🛠️ Prasyarat Sistem (Prerequisites)

Pastikan komputer/laptop Anda telah terpasang:
1. **Git**
2. **Docker & Docker Compose** (wajib untuk PostgreSQL 17 + PostGIS spasial)
3. **PHP >= 8.2** (disarankan PHP 8.2 atau 8.3)
   - Ekstensi PHP wajib aktif: `pdo_pgsql`, `pgsql`, `mbstring`, `fileinfo`, `gd`, `zip`, `openssl`, `curl`
4. **Composer >= 2.x**
5. *(Khusus Developer Mobile)*: **Flutter SDK >= 3.x** & Android Studio / VS Code

---

## 🚀 Panduan Setup Cepat (Anti-Error Clone Guide)

Ikuti langkah-langkah berikut secara berurutan saat pertama kali melakukan clone repositori:

### 1. Clone Repositori
```bash
git clone <URL_REPOSITORY_INI> project-3-polije
cd project-3-polije
```

---

### 2. Nyalakan Database PostgreSQL & PostGIS (Docker)
Aplikasi ini membutuhkan ekstensi **PostGIS** untuk perhitungan spasial (batas poligon 7 kelurahan, koordinat laporan warga, titik bank sampah, dan heatmap).

Dari **root folder repositori** (`project-3-polije/`), jalankan:
```bash
docker compose up -d
```
> **Verifikasi:** Jalankan `docker ps`, pastikan container `papsampah-postgis` berstatus *healthy/running* pada port `5432`.

---

### 3. Setup Backend Laravel

Masuk ke direktori backend:
```bash
cd laravel/laravel
```

#### a. Pasang Dependencies Composer
```bash
composer install
```

#### b. Siapkan File Environment (`.env`)
Salin file `.env.example` ke `.env`:
```bash
cp .env.example .env
```
File `.env.example` sudah otomatis dikonfigurasi ke PostgreSQL lokal:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=papsampah
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

#### c. Generate Application Key & Storage Link
```bash
php artisan key:generate
php artisan storage:link
```
> ⚠️ **PENTING:** `php artisan storage:link` wajib dijalankan agar foto laporan sampah, bukti hasil pembersihan petugas, dan thumbnail berita dapat diakses di browser dan mobile.

#### d. Jalankan Migration & Database Seeder
Jalankan migrasi database beserta data awal (boundaries 7 kelurahan Sumbersari, data master fasilitas, artikel berita, dan akun admin):
```bash
php artisan migrate:fresh --seed
```

#### e. Jalankan Unit & Feature Tests
Pastikan semua test suite lulus sebelum mulai mengembangkan fitur:
```bash
php artisan test
```
*(Seluruh 22 automated tests harus berstatus **PASS**).*

#### f. Jalankan Web Server
```bash
php artisan serve
```
Web Admin & REST API sekarang berjalan di **`http://127.0.0.1:8000`**.

---

### 4. Setup Aplikasi Mobile Flutter (Untuk Tim Mobile)

Buka terminal baru:
```bash
cd mobile-app/mobile
flutter pub get
```

#### Konfigurasi Base URL API:
- Jika menggunakan **Android Emulator**: arahkan API ke `http://10.0.2.2:8000/api`
- Jika menggunakan **Perangkat Fisik (HP via WiFi / USB Debugging)**:
  1. Jalankan Laravel dengan binding ke seluruh IP: `php artisan serve --host=0.0.0.0 --port=8000`
  2. Cari IP laptop Anda di jaringan LAN (contoh: `192.168.1.50`).
  3. Arahkan Base URL Flutter ke `http://192.168.1.50:8000/api`.

Jalankan aplikasi di emulator atau perangkat:
```bash
flutter run
```

---

## 👥 Akun Demo untuk Pengujian & Kolaborasi

Semua password default adalah: `password123`

| Peran (Role) | Email | Keterangan / Akses |
|---|---|---|
| **Super Admin Kecamatan** | `kec.sumbersari@papsampah.id` | Akses penuh monitoring 7 kelurahan, kelola bank sampah, TPA, berita edukasi, rekap semua laporan. |
| **Admin Kelurahan Sumbersari** | `kel.sumbersari@papsampah.id` | Kelola laporan & penugasan petugas di Kelurahan Sumbersari. Berita miliknya sendiri. |
| **Admin Kelurahan Antirogo** | `kel.antirogo@papsampah.id` | Kelola wilayah Kelurahan Antirogo. |
| **Admin Kelurahan Karangrejo** | `kel.karangrejo@papsampah.id` | Kelola wilayah Kelurahan Karangrejo. |
| **Admin Kelurahan Kebonsari** | `kel.kebonsari@papsampah.id` | Kelola wilayah Kelurahan Kebonsari. |
| **Admin Kelurahan Kranjingan** | `kel.kranjingan@papsampah.id` | Kelola wilayah Kelurahan Kranjingan. |
| **Admin Kelurahan Tegalgede** | `kel.tegalgede@papsampah.id` | Kelola wilayah Kelurahan Tegalgede. |
| **Admin Kelurahan Wirolegi** | `kel.wirolegi@papsampah.id` | Kelola wilayah Kelurahan Wirolegi. |

---

## 🗺️ Fitur-Fitur Utama

1. **Peta Spasial & Heatmap Interaktif (`/peta`)**:
   - Menampilkan batas poligon 7 kelurahan Sumbersari (GeoJSON dari PostGIS).
   - Marker titik sampah aktif (merah) & titik selesai H+7 (hijau) dengan komparasi foto *Before vs After*.
   - Layer heatmap kepadatan sampah dinamis (Invariant #7: laporan selesai tidak masuk kalkulasi heatmap).
   - Titik fasilitas Bank Sampah & TPA / TPS-3R.
   - Pustaka Leaflet disimpan lokal di `public/vendor/leaflet/` agar peta langsung ter-load tanpa ketergantungan internet luar.
2. **Manajemen Laporan Sampah (`/laporan`)**:
   - Filter status dinamis (Menunggu Validasi, Tervalidasi, Dalam Penanganan, Selesai, Ditolak).
   - Auto-submit pada filter kategori/kelurahan dan pencarian cepat via tombol *Enter*.
   - Penugasan petugas kebersihan desa dengan instruksi peralatan (*custom equipment notes*).
3. **Bank Sampah & TPA (`/bank-sampah`, `/tpa`)**:
   - Manajemen fasilitas persampahan dengan koordinat akurat.
4. **Edukasi & Berita Lingkungan (`/berita`)**:
   - Proteksi otorisasi: Admin Kelurahan hanya dapat mengelola berita miliknya sendiri dan tidak dapat menghapus berita Kecamatan atau kelurahan lain.
5. **REST API Publik & Mobile**:
   - `/api/auth/...` (Login, Register, Profile, Logout Sanctum)
   - `/api/reports/...` (Pelaporan warga via GPS + Kamera)
   - `/api/worker/...` (Task checklist & bukti pembersihan petugas)
   - `/api/map/...` (Waste points, heatmap, boundaries)
   - `/api/waste-banks` & `/api/landfills`
   - `/api/news`
   - `/api/weather` (Cuaca real-time Open-Meteo Sumbersari, cache 30 menit)
   - `/api/settings`

---

## ❓ FAQ & Panduan Mengatasi Kendala (Troubleshooting)

### 1. `SQLSTATE[08006] [7] could not connect to server: Connection refused`
- **Penyebab:** Container Docker database belum menyala atau port 5432 bertabrakan.
- **Solusi:**
  1. Pastikan Docker Desktop / service Docker aktif.
  2. Di root folder repositori, jalankan `docker compose up -d`.
  3. Cek apakah ada PostgreSQL lokal lain yang bentrok di port 5432 via command `sudo lsof -i :5432` atau `netstat -ano | findstr 5432`.

### 2. `Call to undefined function pg_connect()` atau `pdo_pgsql driver not found`
- **Penyebab:** Ekstensi driver PostgreSQL di PHP belum diaktifkan.
- **Solusi:**
  - **Ubuntu/Debian:** `sudo apt-get install php8.2-pgsql` (atau `php8.3-pgsql`).
  - **Windows:** Buka `php.ini`, hilangkan tanda titik-koma (`;`) pada baris `extension=pdo_pgsql` dan `extension=pgsql`, lalu restart terminal.

### 3. Foto Laporan atau Berita Tidak Muncul (Broken Image / 404)
- **Penyebab:** Symlink public storage belum terbentuk.
- **Solusi:** Jalankan di `laravel/laravel`:
  ```bash
  php artisan storage:link
  ```

### 4. Peta Tidak Menampilkan Peta / Abu-abu
- **Penyebab:** Aset Leaflet tidak terbaca.
- **Solusi:** Aset lokal Leaflet telah disediakan di `public/vendor/leaflet/`. Pastikan folder `public/vendor/leaflet/` ada setelah clone. Jika belum ada, jalankan `php artisan test` untuk memastikan environment siap.

---

## 🤝 Aturan Kolaborasi Git (Git Workflow)

1. **Branching**:
   - Cabang utama adalah `main`.
   - Buat branch baru untuk setiap fitur atau bug fix:
     - `feat/nama-fitur`
     - `fix/nama-bug`
2. **Commit Message**: Gunakan pesan commit yang jelas dan deskriptif.
3. **Sebelum Push**:
   - Jalankan `php artisan test` untuk memastikan tidak ada pengujian yang gagal (*regression-free*).
   - Jangan melakukan commit pada file sensitif (`.env` asli, file session, atau file temp OS).

Selamat berkarya bersama membangun **Pap Sampah** untuk Kecamatan Sumbersari yang lebih bersih dan asri! 🌱
