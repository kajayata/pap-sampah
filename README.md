# 🌿 Pap Sampah — Sistem Informasi & Pengelolaan Sampah Terpadu

> **Wilayah Fokus:** Kecamatan Sumbersari, Kabupaten Jember (7 Kelurahan: Antirogo, Karangrejo, Kebonsari, Kranjingan, Tegalgede, Wirolegi, Sumbersari).

Selamat datang di repositori monorepo **Pap Sampah**. Dokumen ini disusun sebagai panduan lengkap bagi seluruh anggota tim developer (**Backend, Web Admin, dan Mobile Flutter**) untuk melakukan clone repositori, instalasi dependensi, konfigurasi koneksi antar-perangkat, menjalankan automated testing, serta menjalankan aplikasi di laptop masing-masing tanpa kendala error.

---

## 📑 Daftar Isi

- [Struktur Monorepo](#-struktur-monorepo)
- [Prasyarat Sistem (Prerequisites)](#-prasyarat-sistem-prerequisites)
- [Langkah Setup Monorepo](#-langkah-setup-monorepo)
  - [1. Clone Repositori](#1-clone-repositori)
  - [2. Jalankan Database PostgreSQL & PostGIS (Docker)](#2-jalankan-database-postgresql--postgis-docker)
  - [3. Setup Backend & Web Admin Laravel](#3-setup-backend--web-admin-laravel)
  - [4. Setup Aplikasi Mobile Flutter](#4-setup-aplikasi-mobile-flutter-untuk-tim-mobile)
- [Panduan Menghubungkan HP / Mobile ke Backend](#-panduan-menghubungkan-hp--mobile-ke-backend)
  - [Opsi A: HP Fisik via Kabel USB (Direkomendasikan)](#opsi-a-hp-fisik-via-kabel-usb-sangat-direkomendasikan)
  - [Opsi B: HP Fisik via Wi-Fi / Hotspot yang Sama](#opsi-b-hp-fisik-via-wi-fi--hotspot-yang-sama)
  - [Opsi C: Android Emulator](#opsi-c-android-emulator)
- [Panduan Pengujian (Automated Testing)](#-panduan-pengujian-automated-testing)
- [Akun Demo untuk Pengujian & Kolaborasi](#-akun-demo-untuk-pengujian--kolaborasi)
- [Peta Fitur & Endpoint untuk Testing](#-peta-fitur--endpoint-untuk-testing)
- [Panduan Mengatasi Kendala (Troubleshooting)](#-panduan-mengatasi-kendala-troubleshooting)
- [Aturan Kolaborasi Git (Git Workflow)](#-aturan-kolaborasi-git-git-workflow)

---

## 📁 Struktur Monorepo

```text
project-3-polije/
├── docker-compose.yml       # PostgreSQL 17 + PostGIS 3.5 (Spasial)
├── web/
│   └── laravel/             # Backend REST API (Sanctum) + Web Admin (Blade, Tailwind, Leaflet)
├── mobile-app/
│   └── mobile/              # Aplikasi Mobile Warga & Petugas (Flutter SDK)
├── docs/                    # Dokumentasi arsitektur, API contract, flow bisnis, & progress
└── README.md                # Panduan instalasi dan kolaborasi tim (file ini)
```

---

## 🛠️ Prasyarat Sistem (Prerequisites)

Pastikan peralatan berikut sudah terpasang di laptop Anda:

1. **Git**
2. **Docker & Docker Compose** (Wajib untuk PostgreSQL 17 + ekstensi PostGIS spasial)
3. **PHP >= 8.2** (Disarankan PHP 8.2 atau 8.3)
   - Ekstensi PHP wajib aktif: `pdo_pgsql`, `pgsql`, `mbstring`, `fileinfo`, `gd`, `zip`, `openssl`, `curl`
4. **Composer >= 2.x**
5. **Node.js >= 18.x & npm** (Wajib untuk kompilasi aset frontend Vite & Tailwind CSS)
6. *(Khusus Developer Mobile)*:
   - **Flutter SDK >= 3.x**
   - **Android SDK & Platform-Tools (`adb`)**
   - Android Studio atau VS Code dengan ekstensi Flutter & Dart

---

## 🚀 Langkah Setup Monorepo

Ikuti langkah-langkah berikut secara berurutan saat pertama kali menyiapkan project:

### 1. Clone Repositori
```bash
git clone <URL_REPOSITORY_INI> project-3-polije
cd project-3-polije
```

---

### 2. Jalankan Database PostgreSQL & PostGIS (Docker)
Aplikasi ini membutuhkan database spasial **PostGIS** untuk kalkulasi batas poligon 7 kelurahan Sumbersari, verifikasi koordinat laporan warga (`ST_Contains`), titik fasilitas bank sampah/TPA, dan heatmap kepadatan sampah.

Dari **root direktori repositori** (`project-3-polije/`), jalankan:
```bash
docker compose up -d
```

> **Verifikasi:** Jalankan `docker ps`. Pastikan container `papsampah-postgis` berstatus *Up (healthy)* dan memetakan port `5432:5432`.

---

### 3. Setup Backend & Web Admin Laravel

Pindah ke folder backend:
```bash
cd web/laravel
```

#### a. Pasang Dependensi Composer
```bash
composer install
```

#### b. Siapkan File Environment (`.env`)
Salin file `.env.example` ke `.env`:
```bash
cp .env.example .env
```
> Konfigurasi default di `.env.example` sudah disesuaikan langsung dengan container Docker lokal (`DB_CONNECTION=pgsql`, `DB_PORT=5432`, `DB_DATABASE=papsampah`, `DB_USERNAME=postgres`, `DB_PASSWORD=postgres`).

#### c. Generate Application Key & Storage Link
```bash
php artisan key:generate
php artisan storage:link
```
> ⚠️ **PENTING:** `php artisan storage:link` wajib dijalankan agar foto laporan sampah warga, bukti Before/After pembersihan petugas, dan thumbnail berita dapat diakses dari browser dan mobile.

#### d. Jalankan Migrasi & Database Seeder
Jalankan migrasi tabel spasial beserta data awal (boundaries 7 kelurahan Sumbersari, data master kategori sampah, bank sampah, TPA, berita edukasi, serta akun demo):
```bash
php artisan migrate:fresh --seed
```

#### e. Build Aset Frontend (Vite) — *Wajib*
Web Admin menggunakan Blade dengan Tailwind CSS dan Vite. Aset wajib dikompilasi agar tidak muncul error `Vite manifest not found`:
```bash
npm install
npm run build
```
*(Opsional: Jalankan `npm run dev` di terminal terpisah jika Anda sedang aktif mengedit tampilan Blade, CSS, atau JavaScript Web Admin).*

#### f. Jalankan Web Server Laravel
- **Untuk akses Web Admin lokal saja:**
  ```bash
  php artisan serve
  ```
- **Untuk testing bersama Aplikasi Mobile (Direkomendasikan):**
  ```bash
  php artisan serve --host=0.0.0.0 --port=8000
  ```
Akses halaman utama di browser: **`http://127.0.0.1:8000`** atau **`http://localhost:8000`**.

---

### 4. Setup Aplikasi Mobile Flutter (Untuk Tim Mobile)

Buka terminal baru dan masuk ke direktori mobile:
```bash
cd mobile-app/mobile
flutter pub get
```

Pastikan perangkat target (HP fisik atau Emulator) sudah terdeteksi:
```bash
flutter devices
```

Jalankan aplikasi ke perangkat target:
```bash
flutter run
```

---

## 📱 Panduan Menghubungkan HP / Mobile ke Backend

Saat melakukan testing aplikasi Flutter pada HP fisik atau emulator, HP memerlukan rute koneksi menuju backend Laravel di laptop Anda:

### Opsi A: HP Fisik via Kabel USB (Sangat Direkomendasikan)
Metode ini adalah yang paling stabil, cepat, dan **tidak terpengaruh oleh firewall atau batasan router Wi-Fi**.

1. Hubungkan HP Android ke laptop menggunakan kabel USB data.
2. Pastikan **USB Debugging** di HP Anda sudah aktif (dari *Developer Options*).
3. Jalankan perintah `adb reverse` berikut di terminal laptop:
   ```bash
   adb reverse tcp:8000 tcp:8000
   ```
4. Buka aplikasi **Pap Sampah** di HP.
5. Base URL default aplikasi sudah otomatis mengarah ke `http://127.0.0.1:8000/api`, sehingga aplikasi akan langsung terhubung ke backend laptop Anda!

> **Catatan:** Jalankan kembali perintah `adb reverse tcp:8000 tcp:8000` setiap kali kabel USB dilepas dan dipasang ulang.

---

### Opsi B: HP Fisik via Wi-Fi / Hotspot yang Sama
Jika ingin melakukan testing nirkabel (wireless):

1. Hubungkan laptop dan HP ke jaringan Wi-Fi atau Hotspot pribadi yang sama.
2. Jalankan Laravel dengan binding host `0.0.0.0`:
   ```bash
   cd web/laravel
   php artisan serve --host=0.0.0.0 --port=8000
   ```
3. Cari alamat IP lokal laptop Anda:
   - **Windows:** Buka CMD / PowerShell, ketik `ipconfig` (lihat `IPv4 Address`, contoh: `192.168.1.15`).
   - **Linux / macOS:** Buka terminal, ketik `ip a` atau `ifconfig` (contoh: `192.168.1.15`).
4. Pastikan firewall laptop tidak memblokir port 8000.
5. Buka aplikasi **Pap Sampah** di HP:
   - Di halaman Login, klik ikon **Gerigi ⚙️ (Pengaturan Server)** di pojok kanan atas.
   - Ubah Base URL menjadi: `http://<IP_LAPTOP_ANDA>:8000/api` (contoh: `http://192.168.1.15:8000/api`).
   - Klik **Simpan**.

---

### Opsi C: Android Emulator
Jika Anda menjalankan Android Virtual Device (AVD) di Android Studio:
1. Android Emulator menggunakan alias IP khusus `10.0.2.2` untuk mengakses localhost komputer host.
2. Di layar Login aplikasi pada emulator, klik ikon **Gerigi ⚙️ (Pengaturan Server)**.
3. Masukkan Base URL: `http://10.0.2.2:8000/api` lalu klik **Simpan**.

---

## 🧪 Panduan Pengujian (Automated Testing)

Sebelum melakukan commit atau push fitur baru, jalankan automated testing berikut untuk memastikan tidak terjadi regresi (*breaking changes*):

### 1. Pengujian Backend Laravel (PHPUnit / Pest)
```bash
cd web/laravel
php artisan test
```
- Menjalankan **22 test suites** dan **153 assertions**.
- Memvalidasi autentikasi Sanctum, proteksi role, filter poligon spasial PostGIS Sumbersari, kalkulasi heatmap, penugasan multi-petugas, dan API master data.
- **Seluruh pengujian harus berstatus PASS.**

### 2. Pengujian Mobile Flutter
```bash
cd mobile-app/mobile
flutter test
```
- Menjalankan unit & widget test untuk model data, validasi role, fleksibilitas wrapper respons backend, serta komponen UI.
- **Seluruh pengujian harus berstatus PASS.**

---

## 👥 Akun Demo untuk Pengujian & Kolaborasi

Semua password default adalah: **`password123`**

> ⚠️ **ATURAN AKSES PLATFORM (PENTING):**
> - **Akun Admin & Super Admin** hanya dapat login pada **Web Dashboard Admin**. Akun ini tidak diizinkan masuk ke aplikasi Mobile.
> - **Akun Warga & Petugas Kebersihan** digunakan untuk login pada **Aplikasi Mobile Flutter**. Akun ini tidak memiliki akses ke Web Admin.

### 🌐 Akun Web Dashboard Admin

| Peran (Role) | Email Login | Hak Akses & Keterangan |
|---|---|---|
| **Super Admin Kecamatan** | `kec.sumbersari@papsampah.id` | Monitoring 7 kelurahan, kelola Bank Sampah, TPA, berita edukasi, rekap dan statistik seluruh laporan sampah. |
| **Admin Kelurahan Sumbersari** | `kel.sumbersari@papsampah.id` | Validasi laporan warga, penugasan petugas kebersihan, & verifikasi hasil pembersihan Kelurahan Sumbersari. |
| **Admin Kelurahan Antirogo** | `kel.antirogo@papsampah.id` | Pengelolaan laporan & penugasan di Kelurahan Antirogo. |
| **Admin Kelurahan Karangrejo** | `kel.karangrejo@papsampah.id` | Pengelolaan laporan & penugasan di Kelurahan Karangrejo. |
| **Admin Kelurahan Kebonsari** | `kel.kebonsari@papsampah.id` | Pengelolaan laporan & penugasan di Kelurahan Kebonsari. |
| **Admin Kelurahan Kranjingan** | `kel.kranjingan@papsampah.id` | Pengelolaan laporan & penugasan di Kelurahan Kranjingan. |
| **Admin Kelurahan Tegalgede** | `kel.tegalgede@papsampah.id` | Pengelolaan laporan & penugasan di Kelurahan Tegalgede. |
| **Admin Kelurahan Wirolegi** | `kel.wirolegi@papsampah.id` | Pengelolaan laporan & penugasan di Kelurahan Wirolegi. |

---

### 📱 Akun Mobile App (Masyarakat & Petugas)

| Peran (Role) | Email Login | Hak Akses & Keterangan |
|---|---|---|
| **Masyarakat / Warga (Pelapor)** | `warga.sumbersari@example.com` | Membuat laporan sampah dengan foto kamera & GPS otomatis, melihat status laporan secara real-time, melihat peta heatmap, bank sampah, dan berita. *(Bisa juga registrasi akun baru langsung di aplikasi).* |
| **Petugas Kebersihan Sumbersari (1)** | `petugas1.sumbersari@papsampah.id` | Menerima/menolak tugas pembersihan dari admin desa, melihat instruksi alat, upload bukti foto Before/After penanganan. |
| **Petugas Kebersihan Sumbersari (2)** | `petugas2.sumbersari@papsampah.id` | Rekan tim petugas kebersihan di Kelurahan Sumbersari. |
| **Petugas Kebersihan Antirogo** | `petugas1.antirogo@papsampah.id` | Petugas kebersihan operasional Kelurahan Antirogo. |

---

## 🗺️ Peta Fitur & Endpoint untuk Testing

### 1. Web Dashboard
- `/` atau `/home`: Landing page publik dengan ringkasan wilayah Sumbersari dan tombol login.
- `/login`: Form autentikasi web admin.
- `/dashboard`: Statistik laporan aktif, ringkasan per kelurahan, dan widget laporan masuk terbaru.
- `/peta`: Peta spasial interaktif Leaflet.js dengan batas 7 kelurahan, marker titik sampah aktif vs selesai H+7 (Before vs After modal), heatmap kepadatan, bank sampah, dan TPA.
- `/laporan`: Manajemen laporan sampah, filter status dinamis, validasi, dan form penugasan multi-petugas.
- `/bank-sampah`: Manajemen fasilitas bank sampah dengan Leaflet coordinate picker.
- `/tpa`: Manajemen fasilitas TPA / TPS-3R.
- `/berita`: Pengelolaan artikel berita edukasi lingkungan hidup.
- `/workers`: Manajemen data petugas kebersihan per kelurahan.

### 2. REST API Mobile (`/api/...`)
- `/api/auth/login`, `/api/auth/register`, `/api/auth/me`, `/api/auth/logout`: Autentikasi berbasis Sanctum Token.
- `/api/categories`: Daftar kategori sampah terdaftar.
- `/api/reports`: Pelaporan sampah warga (mendukung multipart foto dan koordinat GPS) & riwayat pelaporan.
- `/api/tasks`: Manajemen tugas kebersihan petugas (`accept`, `reject`, `photos` Before/After, `complete`).
- `/api/map/waste-points`, `/api/map/heatmap`, `/api/map/boundaries`: Data spasial GeoJSON untuk visualisasi peta.
- `/api/waste-banks`, `/api/landfills`: Direktori fasilitas persampahan.
- `/api/news`: Berita & artikel edukasi terpublikasi.
- `/api/weather`: Prakiraan cuaca real-time Kecamatan Sumbersari (Open-Meteo dengan cache 30 menit).
- `/api/settings`: Pengaturan konfigurasi sistem publik.

---

## ❓ Panduan Mengatasi Kendala (Troubleshooting)

### 1. "Tidak dapat terhubung ke server backend. pastikan server aktif" di HP
- **Penyebab:** HP tidak dapat menjangkau port 8000 di laptop.
- **Solusi:**
  1. Jika menggunakan **Kabel USB**: Jalankan perintah `adb reverse tcp:8000 tcp:8000` di terminal laptop Anda.
  2. Jika menggunakan **Wi-Fi**:
     - Pastikan server dijalankan dengan `php artisan serve --host=0.0.0.0 --port=8000`.
     - Cek apakah IP laptop sudah sesuai pada menu pengaturan server (ikon gerigi ⚙️ di layar login aplikasi).
     - Periksa firewall (Windows Defender / ufw) agar tidak memblokir koneksi masuk ke port 8000.

### 2. "Vite manifest not found at: ... public/build/manifest.json"
- **Penyebab:** Dependensi frontend belum ter-build setelah clone.
- **Solusi:** Jalankan di folder `web/laravel`:
  ```bash
  npm install && npm run build
  ```

### 3. "Address already in use" saat menjalankan `php artisan serve`
- **Penyebab:** Masih ada proses PHP atau server sebelumnya yang berjalan di port 8000.
- **Solusi:**
  - **Linux / macOS:**
    ```bash
    fuser -k 8000/tcp
    ```
  - **Windows (PowerShell / CMD):**
    ```powershell
    netstat -ano | findstr :8000
    taskkill /PID <PID_YANG_MUNCUL> /F
    ```

### 4. `SQLSTATE[08006] could not connect to server: Connection refused`
- **Penyebab:** Container database PostgreSQL Docker belum menyala.
- **Solusi:** Di root direktori repositori (`project-3-polije/`), jalankan:
  ```bash
  docker compose up -d
  ```
  Lalu verifikasi dengan `docker ps`.

### 5. `Role tidak diizinkan masuk` saat Login
- **Penyebab:** Mencoba login ke aplikasi Mobile menggunakan akun Admin/Super Admin, atau sebaliknya.
- **Solusi:**
  - Login Web Admin hanya untuk akun ber-role `super_admin_kecamatan` dan `admin_desa`.
  - Login Mobile hanya untuk akun ber-role `masyarakat` dan `petugas_desa`.
  - Gunakan email yang sesuai pada tabel [Akun Demo](#-akun-demo-untuk-pengujian--kolaborasi).

### 6. Foto Laporan / Thumbnail Berita Tidak Tampil (Broken Image / 404)
- **Penyebab:** Symlink public storage Laravel belum dibuat.
- **Solusi:** Jalankan di `web/laravel`:
  ```bash
  php artisan storage:link
  ```

---

## 🤝 Aturan Kolaborasi Git (Git Workflow)

1. **Branch Utama:**
   - Cabang utama adalah `main`. Semua branch fitur harus berasal dari `main` terbaru (`git pull origin main`).
2. **Penamaan Branch:**
   - Fitur baru: `feat/nama-fitur` (contoh: `feat/notifikasi-fcm`, `feat/export-laporan-pdf`)
   - Perbaikan bug: `fix/nama-bug` (contoh: `fix/layout-overflow-detail-laporan`)
3. **Standar Sebelum Melakukan Commit / Push:**
   - Jalankan automated tests:
     ```bash
     # Di folder web/laravel
     php artisan test

     # Di folder mobile-app/mobile
     flutter test
     ```
   - Pastikan tidak ada conflict markers (`<<<<<<<`, `=======`, `>>>>>>>`) yang tertinggal.
   - Jangan pernah melakukan commit pada file rahasia `.env`, kredensial, atau file build lokal (`build/`, `.dart_tool/`).

---

Selamat berkolaborasi dan berkarya bersama membangun **Pap Sampah** untuk Kecamatan Sumbersari yang lebih bersih, asri, dan terkelola secara modern! 🌱
