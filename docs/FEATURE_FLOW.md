# FEATURE FLOW — PAP SAMPAH

> Dokumen ini menjelaskan alur kerja setiap fitur pada aplikasi PAP Sampah (Pengelolaan Aktivitas Persampahan) secara lengkap: dari sisi mobile Flutter, REST API, Laravel backend (Controller → Service → Model), hingga database.

---

## ARSITEKTUR SISTEM

```
┌─────────────────────────────────────────────┐
│            MOBILE APP (Flutter)             │
│  lib/views/ ─ lib/services/ ─ lib/providers/│
└─────────────────┬───────────────────────────┘
                  │ HTTP REST API (JSON)
                  ▼
┌─────────────────────────────────────────────┐
│        WEB BACKEND (Laravel 11)             │
│  routes/api.php + routes/web.php            │
│  app/Http/Controllers/{Api,Web}/            │
│  app/Services/                              │
│  app/Models/                                │
└─────────────────┬───────────────────────────┘
                  │ Eloquent ORM + raw SQL PostGIS
                  ▼
┌─────────────────────────────────────────────┐
│         DATABASE (SQLite / PostgreSQL)       │
│  Migrations: database/migrations/           │
└─────────────────────────────────────────────┘
```

**4 Role Pengguna:**
| Role | Platform | Akses |
|---|---|---|
| `super_admin_kecamatan` | Web saja | Semua data lintas kelurahan |
| `admin_desa` | Web saja | Hanya data kelurahannya |
| `masyarakat` | Mobile saja | Laporan & info publik |
| `petugas_desa` | Mobile saja | Tugas pembersihan |

---

## DAFTAR FITUR

1. [Autentikasi Mobile (Login/Register/Logout)](#1-autentikasi-mobile)
2. [Login Web Admin](#2-login-web-admin)
3. [Dashboard Monitoring](#3-dashboard-monitoring)
4. [Laporan Sampah — Buat Laporan (Masyarakat)](#4-laporan-sampah--buat-laporan)
5. [Laporan Sampah — Validasi & Penolakan (Admin)](#5-laporan-sampah--validasi--penolakan)
6. [Penugasan Pembersihan / Cleanup Task (Admin)](#6-penugasan-pembersihan--cleanup-task)
7. [Tugas Pembersihan — Petugas (Mobile)](#7-tugas-pembersihan--petugas-mobile)
8. [Verifikasi Hasil Pembersihan (Admin)](#8-verifikasi-hasil-pembersihan)
9. [Peta Spasial & Heatmap](#9-peta-spasial--heatmap)
10. [Informasi Cuaca](#10-informasi-cuaca)
11. [Bank Sampah](#11-bank-sampah)
12. [TPA / TPS-3R (Landfill)](#12-tpa--tps-3r-landfill)
13. [Berita & Edukasi Lingkungan](#13-berita--edukasi-lingkungan)
14. [CRUD Petugas Kebersihan (Admin Web)](#14-crud-petugas-kebersihan)
15. [CRUD Admin Kelurahan (Super Admin Web)](#15-crud-admin-kelurahan)
16. [Manajemen User Masyarakat (Admin Web)](#16-manajemen-user-masyarakat)
17. [Upload & Kompresi Foto](#17-upload--kompresi-foto)

---

## 1. Autentikasi Mobile

**Deskripsi:** Registrasi, login, dan logout untuk pengguna mobile (Masyarakat & Petugas Kebersihan) menggunakan Laravel Sanctum.

### Flow: Register Masyarakat

```
[Mobile: register_screen.dart]
    │ Input: name, email, password, password_confirmation, phone
    │ AuthService.register()
    ▼
POST /api/auth/register
    │
    ▼
[Api\AuthController@register]
    │ Validate: name, email(unique), password(min:8, confirmed), phone
    │ Role::where('name','masyarakat')->firstOrFail()
    │ User::create([role_id, name, email, password, phone, is_active=true])
    │ $user->createToken('auth-token')->plainTextToken
    ▼
Response: { user, token, data }
    │
    ▼
[Mobile: TokenStorage.saveAuth(token, user)]
    │ SharedPreferences menyimpan token & user JSON
    ▼
Navigasi ke CitizenMainScreen
```

### Flow: Login

```
[Mobile: login_screen.dart]
    │ Input: email, password
    │ AuthService.login()
    ▼
POST /api/auth/login
    │
    ▼
[Api\AuthController@login]
    │ User::with('role')->where('email')->first()
    │ Hash::check(password) && user->is_active
    │ $user->createToken('auth-token')->plainTextToken
    ▼
Response: { user, token }
    │
    ▼
[Mobile: TokenStorage.saveAuth()]
    │ AuthProvider.setUser() => notifyListeners()
    ▼
Navigasi berdasarkan role:
  - masyarakat   => CitizenMainScreen
  - petugas_desa => WorkerMainScreen
```

### Flow: Logout

```
[Mobile: settings/profil button]
    │ AuthService.logout()
    ▼
POST /api/auth/logout  (Bearer token)
    │
    ▼
[Api\AuthController@logout]
    │ $request->user()->currentAccessToken()->delete()
    ▼
[Mobile: TokenStorage.clear()]
    │ AuthProvider.logout()
    ▼
Kembali ke OnboardingScreen / LoginScreen
```

### File Terkait

| Layer | File |
|---|---|
| Mobile UI | `mobile-app/mobile/lib/views/auth/login_screen.dart` |
| Mobile UI | `mobile-app/mobile/lib/views/auth/register_screen.dart` |
| Mobile UI | `mobile-app/mobile/lib/views/auth/onboarding_screen.dart` |
| Mobile Service | `mobile-app/mobile/lib/services/auth_service.dart` |
| Mobile State | `mobile-app/mobile/lib/providers/auth_provider.dart` |
| Mobile Model | `mobile-app/mobile/lib/models/user.dart` |
| Mobile Storage | `mobile-app/mobile/lib/core/storage/` (TokenStorage) |
| API Constants | `mobile-app/mobile/lib/core/constants/api_constants.dart` |
| API Route | `web/laravel/routes/api.php` baris 19-25 |
| API Controller | `web/laravel/app/Http/Controllers/Api/AuthController.php` |
| Model | `web/laravel/app/Models/User.php` |
| Model | `web/laravel/app/Models/Role.php` |
| Database | `database/migrations/..._create_users_table.php` |
| Database | `database/migrations/..._create_personal_access_tokens_table.php` |

---

## 2. Login Web Admin

**Deskripsi:** Autentikasi berbasis session untuk Super Admin dan Admin Kelurahan menggunakan Laravel Auth (bukan Sanctum).

### Flow: Login Web

```
[Browser: GET /login]
    ▼
[Web\AuthController@showLogin]
    │ Jika sudah login => redirect berdasarkan role
    ▼
View: resources/views/auth/login.blade.php

[Browser: POST /login]
    │ Input: email, password, remember
    ▼
[Web\AuthController@login]
    │ Auth::attempt(credentials, remember)
    │ Cek: is_active, village exists & active (untuk admin_desa)
    │ $request->session()->regenerate()
    ▼
redirectByRole():
  - super_admin_kecamatan => /dashboard
  - admin_desa            => /dashboard
  - lainnya               => /
```

### File Terkait

| Layer | File |
|---|---|
| Web Route | `web/laravel/routes/web.php` baris 20-25 |
| Web Controller | `web/laravel/app/Http/Controllers/Web/AuthController.php` |
| View | `web/laravel/resources/views/auth/` |

---

## 3. Dashboard Monitoring

**Deskripsi:** Halaman utama admin menampilkan statistik laporan, tren 7 hari, kategori terbanyak, kelurahan paling banyak laporan, dan petugas teraktif bulan ini.

### Flow

```
[Browser: GET /dashboard]
    │ (auth middleware)
    ▼
[Web\AuthController@dashboard]
    │
    ├─ Scope berdasarkan role:
    │    super_admin => semua laporan
    │    admin_desa  => hanya village_id-nya sendiri
    │
    ├─ WasteReport::query()->selectRaw(COUNT per status)
    │    => total, pending, validated, assigned,
    │       in_progress, pending_verification, resolved, rejected
    │
    ├─ Tren 7 hari (to_char + GROUP BY tanggal)
    │
    ├─ Top 5 kategori terbanyak
    │
    ├─ Top 5 kelurahan laporan aktif (super_admin only)
    │
    └─ Top 5 petugas teraktif bulan ini (cleanup_task_workers COMPLETED)
    ▼
View: resources/views/dashboard/index.blade.php
```

### File Terkait

| Layer | File |
|---|---|
| Web Route | `web/laravel/routes/web.php` baris 29 |
| Web Controller | `web/laravel/app/Http/Controllers/Web/AuthController.php` (method `dashboard`, baris 75-212) |
| Model | `web/laravel/app/Models/WasteReport.php` |
| View | `web/laravel/resources/views/dashboard/` |
| Database | `waste_reports`, `users`, `cleanup_task_workers`, `waste_categories`, `villages` |

---

## 4. Laporan Sampah — Buat Laporan

**Deskripsi:** Masyarakat membuat laporan sampah dari mobile dengan foto, koordinat GPS, dan kategori. Backend memvalidasi lokasi menggunakan PostGIS (hanya dalam wilayah Kecamatan Sumbersari).

### Flow

```
[Mobile: create_report_screen.dart]
    │ 1. Ambil GPS (Geolocator package)
    │ 2. Pilih kategori dari GET /api/categories
    │ 3. Ambil/pilih foto (camera atau gallery)
    │ 4. Isi deskripsi (opsional)
    │ ReportService.submitReport()
    ▼
POST /api/reports  (Bearer token, multipart/form-data)
    Fields: category_id, latitude, longitude, description
    Files:  photos[] (JPEG/PNG/WEBP, max 10MB)
    │
    ▼
[Api\WasteReportController@store]
    │ StoreWasteReportRequest => validasi field
    │ WasteReportService->createReport(reporter, data, photoFiles)
    │
    ▼
[WasteReportService@createReport]
    │
    ├─ findVillageByCoordinates(lat, lng)
    │    => Village::whereRaw('ST_Contains(boundary, ST_SetSRID(ST_MakePoint(lng,lat), 4326))')
    │    Jika tidak ada => ValidationException: "di luar wilayah Sumbersari"
    │
    ├─ DB::transaction:
    │    ├─ Generate report_code: "REP-YYYYMMDD-XXXXX" (unique)
    │    ├─ DB::table('waste_reports')->insertGetId([
    │    │    location = ST_SetSRID(ST_MakePoint(lng,lat), 4326),
    │    │    status   = 'PENDING_VALIDATION'
    │    │  ])
    │    ├─ foreach photos:
    │    │    ImageStorageService->processAndStore(file, 'reports')
    │    │    => auto-orient EXIF, resize <=1280px, compress JPEG 70%
    │    │    DB::table('waste_report_photos')->insert([...])
    │    └─ WasteReportStatusHistory::create([status='PENDING_VALIDATION'])
    │
    ▼
Response: { status:'success', data: WasteReport + photos }
    │
    ▼
[Mobile: ReportProvider update list]
    │ Navigasi ke report_detail_screen.dart
```

### Status Awal
`PENDING_VALIDATION` — menunggu admin validasi

### File Terkait

| Layer | File |
|---|---|
| Mobile UI | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` |
| Mobile UI | `mobile-app/mobile/lib/views/citizen/report_list_screen.dart` |
| Mobile UI | `mobile-app/mobile/lib/views/citizen/report_detail_screen.dart` |
| Mobile Service | `mobile-app/mobile/lib/services/report_service.dart` |
| Mobile Provider | `mobile-app/mobile/lib/providers/report_provider.dart` |
| Mobile Model | `mobile-app/mobile/lib/models/waste_report.dart` |
| API Route | `web/laravel/routes/api.php` baris 74-76 |
| API Controller | `web/laravel/app/Http/Controllers/Api/WasteReportController.php` |
| Request Validator | `web/laravel/app/Http/Requests/StoreWasteReportRequest.php` |
| Service | `web/laravel/app/Services/WasteReportService.php` |
| Service | `web/laravel/app/Services/ImageStorageService.php` |
| Model | `web/laravel/app/Models/WasteReport.php` |
| Model | `web/laravel/app/Models/WasteReportPhoto.php` |
| Model | `web/laravel/app/Models/WasteReportStatusHistory.php` |
| Database | `waste_reports`, `waste_report_photos`, `waste_report_status_histories`, `villages` |

---

## 5. Laporan Sampah — Validasi & Penolakan

**Deskripsi:** Admin Kelurahan (atau Super Admin) meninjau laporan yang masuk, lalu menyetujui atau menolaknya dengan alasan wajib.

### Flow: Validasi

```
[Web: /laporan/{id}]
    ▼
[Web\ReportController@show]
    │ WasteReport::withCoordinates()->with([category, village,
    │   reporter, validator, photos, statusHistories,
    │   cleanupTask.workers, cleanupTask.photos])
    │ Proteksi: admin_desa hanya bisa akses laporan village-nya sendiri
    │ Ambil daftar petugas aktif di kelurahan laporan
    ▼
View: resources/views/reports/show.blade.php
    │ Tombol "Setujui Laporan" => POST /laporan/{id}/validate
    ▼
[Web\ReportController@validateReport]
    │
    ▼
[WasteReportService@validateReport]
    │ authorizeAdminAction(report, admin)
    │    admin_desa   => cek village_id cocok dengan laporan
    │    super_admin  => langsung lanjut
    │ Hanya PENDING_VALIDATION yang bisa divalidasi
    │ DB::transaction:
    │    WasteReport->update([status='VALIDATED', validated_by, validated_at])
    │    WasteReportStatusHistory::create([status='VALIDATED'])
    ▼
Redirect ke /laporan/{id} dengan flash success
```

### Flow: Penolakan

```
[Web: /laporan/{id}]
    │ Admin isi alasan di form => POST /laporan/{id}/reject
    ▼
[Web\ReportController@rejectReport]
    │ Validate: reason (required, min:5, max:500)
    ▼
[WasteReportService@rejectReport]
    │ Hanya PENDING_VALIDATION yang bisa ditolak
    │ DB::transaction:
    │    WasteReport->update([status='REJECTED'])
    │    WasteReportStatusHistory::create([status='REJECTED', note=reason])
    ▼
Redirect ke /laporan/{id}
```

### Status Flow
```
PENDING_VALIDATION => VALIDATED  (Disetujui admin)
PENDING_VALIDATION => REJECTED   (Ditolak admin)
```

### File Terkait

| Layer | File |
|---|---|
| Web Route | `web/laravel/routes/web.php` baris 94-101 |
| Web Controller | `web/laravel/app/Http/Controllers/Web/ReportController.php` |
| Service | `web/laravel/app/Services/WasteReportService.php` |
| Model | `web/laravel/app/Models/WasteReport.php` |
| View | `web/laravel/resources/views/reports/show.blade.php` |
| Database | `waste_reports`, `waste_report_status_histories` |

---

## 6. Penugasan Pembersihan — Cleanup Task

**Deskripsi:** Admin Kelurahan memilih satu atau lebih petugas dan membuat instruksi kerja untuk laporan yang sudah divalidasi.

### Flow

```
[Web: /laporan/{id}]
    │ Form: pilih petugas (checkbox multi), isi instruksi
    │ POST /laporan/{id}/assign
    ▼
[Web\ReportController@assignTask]
    │ Validate: worker_ids[] (required, min:1, exists:users,id)
    │ Validate: notes (nullable, max:1000)
    ▼
[CleanupTaskService@assignTask]
    │
    ├─ authorizeAdmin(report, admin)
    ├─ Cek status: hanya VALIDATED atau ASSIGNED yang bisa ditugaskan
    ├─ Validasi petugas: harus village_id sama, is_active=true, role=petugas_desa
    │
    └─ DB::transaction:
         ├─ CleanupTask::firstOrNew(['report_id' => id])
         │    => set: assigned_by, status='ASSIGNED', notes, assigned_at
         │    => save()
         ├─ foreach workers:
         │    CleanupTaskWorker::updateOrCreate([task_id, worker_id],
         │      [status='PENDING', reset timestamps])
         ├─ Hapus worker yang tidak terpilih lagi
         ├─ WasteReport->update([status='ASSIGNED'])
         └─ WasteReportStatusHistory::create([status='ASSIGNED', note=nama petugas])
    ▼
Redirect ke /laporan/{id}
```

### Status Flow
```
VALIDATED => ASSIGNED  (cleanup_task dibuat/diperbarui)
```

### File Terkait

| Layer | File |
|---|---|
| Web Route | `web/laravel/routes/web.php` baris 99 |
| Web Controller | `web/laravel/app/Http/Controllers/Web/ReportController.php` (method `assignTask`, baris 183-214) |
| Service | `web/laravel/app/Services/CleanupTaskService.php` (method `assignTask`, baris 32-108) |
| Model | `web/laravel/app/Models/CleanupTask.php` |
| Model | `web/laravel/app/Models/CleanupTaskWorker.php` |
| Database | `cleanup_tasks`, `cleanup_task_workers`, `waste_reports`, `waste_report_status_histories` |

---

## 7. Tugas Pembersihan — Petugas (Mobile)

**Deskripsi:** Petugas melihat daftar tugas yang diterima, menerima/menolak tugas, upload foto BEFORE/AFTER, dan menyelesaikan tugas dari aplikasi mobile.

### Flow: Lihat Daftar Tugas

```
[Mobile: worker_main_screen.dart]
    │ WorkerService.getTasks(status?)
    ▼
GET /api/tasks  (Bearer token, query: ?status=)
    │
    ▼
[Api\WorkerTaskController@index]
    │ CleanupTask::whereHas('workers', worker_id)
    │ Optional filter berdasarkan my_status petugas
    │ Paginate 15
    │ Map: tambahkan my_status dari CleanupTaskWorker petugas ini
    ▼
Response: { data: tasks[], meta: pagination }
```

### Flow: Terima Tugas

```
[Mobile: worker_task_detail_screen.dart]
    │ Tap "Terima Tugas" => WorkerService.acceptTask(id)
    ▼
POST /api/tasks/{id}/accept  (Bearer token)
    │
    ▼
[Api\WorkerTaskController@accept]
    │ CleanupTask::findOrFail(id)
    ▼
[CleanupTaskService@acceptTask]
    │ CleanupTaskWorker: update status='ACCEPTED', accepted_at, started_at
    │ Jika task masih ASSIGNED   => CleanupTask status='IN_PROGRESS', started_at=now
    │ Jika report masih ASSIGNED => WasteReport status='IN_PROGRESS'
    │ WasteReportStatusHistory::create([status='IN_PROGRESS'])
    ▼
Response: { status:'success', data: assignment }
```

### Flow: Tolak Tugas

```
[Mobile: worker_task_detail_screen.dart]
    │ Dialog input alasan => WorkerService.rejectTask(id, reason)
    ▼
POST /api/tasks/{id}/reject  (body: { reason })
    │
    ▼
[Api\WorkerTaskController@reject]
    │ Validate: reason (required, min:5, max:500)
    ▼
[CleanupTaskService@rejectTask]
    │ CleanupTaskWorker: update status='REJECTED', rejection_reason=reason
    │ WasteReportStatusHistory::create([note="Petugas {name} menolak..."])
    ▼
Response: { status:'success' }
```

### Flow: Upload Foto BEFORE / AFTER

```
[Mobile: worker_task_detail_screen.dart]
    │ Pilih foto => WorkerService.uploadPhoto(taskId, file, type, lat?, lng?)
    ▼
POST /api/tasks/{id}/photos  (multipart: photo, type, latitude?, longitude?)
    │
    ▼
[Api\WorkerTaskController@uploadPhoto]
    │ Validate: type(BEFORE|AFTER), photo(image, max:10MB), lat?, lng?
    ▼
[CleanupTaskService@uploadTaskPhoto]
    │ Cek CleanupTaskWorker => petugas harus assigned ke task ini
    │ ImageStorageService->processAndStore(file, 'cleanup')
    │    => auto-orient, resize, JPEG 70%
    │ DB::table('cleanup_task_photos')->insertGetId([
    │    task_id, type, storage_key, location=ST_MakePoint(lng,lat)
    │  ])
    ▼
Response: { status:'success', data: photo }
```

### Flow: Selesaikan Tugas

```
[Mobile: tombol "Selesai Tugas"]
    │ WorkerService.completeTask(id)
    ▼
POST /api/tasks/{id}/complete  (Bearer token)
    │
    ▼
[Api\WorkerTaskController@complete]
    ▼
[CleanupTaskService@completeTaskByWorker]
    │ Cek status worker harus ACCEPTED
    │ Cek ada foto AFTER => wajib minimal 1 (jika tidak ada => ValidationException)
    │ CleanupTaskWorker: update status='COMPLETED', completed_at
    │
    │ Cek: apakah semua petugas ACCEPTED sudah COMPLETED?
    │   ├─ Ya (remainingAccepted == 0):
    │   │    CleanupTask: status='PENDING_VERIFICATION'
    │   │    WasteReport: status='PENDING_VERIFICATION'
    │   │    StatusHistory: "Seluruh petugas selesai, menunggu verifikasi..."
    │   └─ Tidak:
    │        StatusHistory: "Petugas {name} selesai, menunggu yang lain"
    ▼
Response: { status:'success' }
```

### Status Flow Petugas (CleanupTaskWorker)
```
PENDING => ACCEPTED (terima tugas) => COMPLETED (selesai)
PENDING => REJECTED (tolak tugas)
COMPLETED => ACCEPTED  (jika admin minta pembersihan ulang)
```

### File Terkait

| Layer | File |
|---|---|
| Mobile UI | `mobile-app/mobile/lib/views/worker/worker_main_screen.dart` |
| Mobile UI | `mobile-app/mobile/lib/views/worker/worker_task_detail_screen.dart` |
| Mobile Service | `mobile-app/mobile/lib/services/worker_service.dart` |
| Mobile Provider | `mobile-app/mobile/lib/providers/worker_task_provider.dart` |
| Mobile Model | `mobile-app/mobile/lib/models/cleanup_task.dart` |
| API Route | `web/laravel/routes/api.php` baris 79-84 |
| API Controller | `web/laravel/app/Http/Controllers/Api/WorkerTaskController.php` |
| Service | `web/laravel/app/Services/CleanupTaskService.php` |
| Model | `web/laravel/app/Models/CleanupTask.php` |
| Model | `web/laravel/app/Models/CleanupTaskWorker.php` |
| Model | `web/laravel/app/Models/CleanupTaskPhoto.php` |
| Database | `cleanup_tasks`, `cleanup_task_workers`, `cleanup_task_photos`, `waste_reports` |

---

## 8. Verifikasi Hasil Pembersihan

**Deskripsi:** Admin Kelurahan meninjau foto AFTER dari petugas dan memverifikasi hasilnya. Bisa disetujui (laporan RESOLVED) atau diminta pembersihan ulang.

### Flow: Setujui (RESOLVED)

```
[Web: /laporan/{id}]
    │ Tombol "Verifikasi Selesai" => POST /laporan/{id}/verify
    ▼
[Web\ReportController@verifyReport]
    │ WasteReport::with('cleanupTask')->findOrFail(id)
    ▼
[CleanupTaskService@verifyAndResolve]
    │ authorizeAdmin(report, admin)
    │ Hanya status PENDING_VERIFICATION yang bisa diverifikasi
    │ DB::transaction:
    │    CleanupTask: status='COMPLETED'
    │    WasteReport: status='RESOLVED', resolved_at=now()
    │    StatusHistory: "Hasil pembersihan diverifikasi => RESOLVED"
    ▼
Redirect ke /laporan/{id} sukses
```

### Flow: Tolak Verifikasi (Pembersihan Ulang)

```
[Web: form alasan => POST /laporan/{id}/reject-verification]
    ▼
[Web\ReportController@rejectVerification]
    │ Validate: notes (required, min:5, max:500)
    ▼
[CleanupTaskService@rejectVerification]
    │ CleanupTask: status='VERIFICATION_REJECTED', notes=catatan admin
    │ CleanupTaskWorker: status COMPLETED => kembali ke ACCEPTED (bisa upload lagi)
    │ WasteReport: status='IN_PROGRESS'
    │ StatusHistory: "Pembersihan ulang diminta: {catatan}"
    ▼
Redirect ke /laporan/{id}
```

### Status Flow Lengkap Laporan (WasteReport)

```
PENDING_VALIDATION
    ├─> VALIDATED          (Admin setujui)
    │     └─> ASSIGNED     (Admin tugaskan petugas)
    │           └─> IN_PROGRESS      (Petugas accept)
    │                 └─> PENDING_VERIFICATION  (Semua petugas selesai)
    │                       ├─> RESOLVED        (Admin verifikasi OK)
    │                       └─> IN_PROGRESS     (Admin minta pembersihan ulang)
    └─> REJECTED           (Admin tolak)
```

### File Terkait

| Layer | File |
|---|---|
| Web Route | `web/laravel/routes/web.php` baris 100-101 |
| Web Controller | `web/laravel/app/Http/Controllers/Web/ReportController.php` (baris 219-270) |
| Service | `web/laravel/app/Services/CleanupTaskService.php` (baris 302-374) |
| Model | `web/laravel/app/Models/CleanupTask.php` |
| Model | `web/laravel/app/Models/WasteReport.php` |
| View | `web/laravel/resources/views/reports/` |
| Database | `cleanup_tasks`, `cleanup_task_workers`, `waste_reports`, `waste_report_status_histories` |

---

## 9. Peta Spasial & Heatmap

**Deskripsi:** Visualisasi interaktif titik laporan sampah di peta (flutter_map di mobile, Leaflet.js di web), heatmap intensitas, dan batas wilayah kelurahan (GeoJSON). Menggunakan PostGIS untuk kueri spasial.

### Flow: Waste Points (Titik Laporan di Peta)

```
[Mobile: spatial_map_tab.dart  |  Web: /peta]
    │ PublicService.getWastePoints(villageId?, type?)
    ▼
GET /api/map/waste-points?type=all&village_id=?
    │
    ▼
[Api\MapApiController@wastePoints]
    │
    ▼
[MapService@getWastePoints]
    │ Aturan tampil berdasarkan AppSetting 'marker_display_days':
    │   'active'   => status PENDING/VALIDATED/ASSIGNED/IN_PROGRESS/PENDING_VERIFICATION
    │   'resolved' => status RESOLVED, resolved_at >= now - marker_display_days
    │   'all'      => gabungan active + resolved dalam window waktu
    │
    │ WasteReport::withCoordinates()
    │   => ST_Y(location) as latitude, ST_X(location) as longitude
    │   ->with([category, village, photos, cleanupTask.photos(type=AFTER)])
    │
    │ Map setiap laporan ke array:
    │   { id, report_code, lat, lng, status, category_name,
    │     village_name, before_photos[], after_photos[], ... }
    ▼
Response: { data: { total_points, points[] } }
```

### Flow: Heatmap

```
GET /api/map/heatmap?village_id=?
    │
    ▼
[MapService@getHeatmapData]
    │ HANYA laporan AKTIF (RESOLVED dikeluarkan sepenuhnya — Invariant #7)
    │ Weight intensity per status:
    │   PENDING_VALIDATION   => 1.0
    │   VALIDATED            => 0.9
    │   ASSIGNED/IN_PROGRESS => 0.7
    │   PENDING_VERIFICATION => 0.5
    ▼
Response: { data: { total_points, heatmap_points: [[lat, lng, weight]] } }
```

### Flow: Batas Wilayah (GeoJSON)

```
GET /api/map/boundaries?village_id=?
    │
    ▼
[MapService@getVillagesGeoJson]
    │ DB::table('villages')->select(ST_AsGeoJSON(boundary))
    │ Map ke GeoJSON FeatureCollection
    ▼
Response: { data: { type:'FeatureCollection', features:[] } }
```

### File Terkait

| Layer | File |
|---|---|
| Mobile UI | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` |
| Mobile Service | `mobile-app/mobile/lib/services/public_service.dart` |
| Mobile Model | `mobile-app/mobile/lib/models/waste_map_point.dart` |
| Mobile Provider | `mobile-app/mobile/lib/providers/public_provider.dart` |
| Web Route | `web/laravel/routes/web.php` baris 32 |
| Web Controller | `web/laravel/app/Http/Controllers/Web/MapWebController.php` |
| API Route | `web/laravel/routes/api.php` baris 40-42 |
| API Controller | `web/laravel/app/Http/Controllers/Api/MapApiController.php` |
| Service | `web/laravel/app/Services/MapService.php` |
| Model Scope | `web/laravel/app/Models/WasteReport.php` (scopeWithCoordinates, baris 96-103) |
| Model | `web/laravel/app/Models/Village.php` |
| View Web | `web/laravel/resources/views/map/` |
| Database | `waste_reports` (kolom `location` GEOMETRY), `villages` (kolom `boundary` GEOMETRY) |
| AppSetting | Kunci `marker_display_days` di tabel `app_settings` |

---

## 10. Informasi Cuaca

**Deskripsi:** Menampilkan prakiraan cuaca real-time Kecamatan Sumbersari, Jember melalui API eksternal Open-Meteo (gratis, tanpa API key). Di-cache 30 menit untuk efisiensi.

### Flow

```
[Mobile: citizen_home_tab.dart (widget cuaca)]
    │ PublicService.getWeather()
    ▼
GET /api/weather
    │
    ▼
[Api\PublicInfoApiController@weather]
    │ WeatherService->getCurrentWeather()
    │
    ▼
[WeatherService@getCurrentWeather]
    │ Cache::remember('weather_sumbersari', 1800 detik, function() {
    │    HTTP GET https://api.open-meteo.com/v1/forecast
    │      params: latitude=-8.1724, longitude=113.7153
    │              timezone=Asia/Jakarta
    │              current=temperature_2m, relative_humidity_2m,
    │                      weather_code, wind_speed_10m
    │    interpretWeatherCode(code) => { condition, description, icon }
    │    Return: { temperature, humidity, wind_speed, condition, icon, ... }
    │ })
    │ Jika API gagal => fallback data cuaca tropis default
    ▼
Response: { temperature:28.5, humidity:78, wind_speed:9.5, condition:'Cerah', icon:'sunny', ... }
    │
    ▼
[Mobile: WeatherData.fromJson()]
    │ Tampil di widget cuaca pada home tab
```

### File Terkait

| Layer | File |
|---|---|
| Mobile UI | `mobile-app/mobile/lib/views/citizen/citizen_home_tab.dart` |
| Mobile Service | `mobile-app/mobile/lib/services/public_service.dart` (baris 88-99) |
| Mobile Model | `mobile-app/mobile/lib/models/weather_data.dart` |
| API Route | `web/laravel/routes/api.php` baris 57 |
| API Controller | `web/laravel/app/Http/Controllers/Api/PublicInfoApiController.php` |
| Service | `web/laravel/app/Services/WeatherService.php` |
| External API | `https://api.open-meteo.com/v1/forecast` |
| Cache Key | `weather_sumbersari` (TTL: 1800 detik / 30 menit) |

---

## 11. Bank Sampah

**Deskripsi:** Direktori bank sampah di wilayah Kecamatan Sumbersari. Dapat dilihat publik di mobile tanpa login. Dikelola (CRUD) oleh admin di web dashboard.

### Flow: Mobile (Lihat Daftar)

```
[Mobile: waste_banks_screen.dart]
    │ PublicService.getWasteBanks(villageId?, search?)
    ▼
GET /api/waste-banks?village_id=?&search=?
    │
    ▼
[Api\WasteBankApiController@index]
    │ WasteBank::where('is_active', true)->with(['village'])
    │ Filter: village_id, search (name/address)
    ▼
Response: { data: { waste_banks[], total } }
```

### Flow: Web CRUD (Admin)

```
[Web: /bank-sampah]
    ▼
[Web\WasteBankController@index / create / store / edit / update / destroy / toggleStatus]
    │ CRUD lengkap untuk tabel waste_banks
    │ Validasi: name, address, phone, village_id, latitude, longitude, is_active
    ▼
View: resources/views/waste_banks/
```

### File Terkait

| Layer | File |
|---|---|
| Mobile UI | `mobile-app/mobile/lib/views/citizen/waste_banks_screen.dart` |
| Mobile Service | `mobile-app/mobile/lib/services/public_service.dart` (baris 29-47) |
| Mobile Model | `mobile-app/mobile/lib/models/waste_bank.dart` |
| API Route | `web/laravel/routes/api.php` baris 45-46 |
| API Controller | `web/laravel/app/Http/Controllers/Api/WasteBankApiController.php` |
| Web Route | `web/laravel/routes/web.php` baris 103-110 |
| Web Controller | `web/laravel/app/Http/Controllers/Web/WasteBankController.php` |
| Model | `web/laravel/app/Models/WasteBank.php` |
| View | `web/laravel/resources/views/waste_banks/` |
| Database | `waste_banks` |

---

## 12. TPA / TPS-3R (Landfill)

**Deskripsi:** Direktori fasilitas pembuangan akhir sampah (TPA) dan TPS-3R. Dapat dilihat publik di mobile. Dikelola (CRUD) admin di web.

### Flow: Mobile (Lihat)

```
[Mobile: citizen_home_tab.dart atau tab informasi]
    │ PublicService.getLandfills()
    ▼
GET /api/landfills
    │
    ▼
[Api\LandfillApiController@index]
    │ Landfill::where('is_active', true)->with(['village'])
    ▼
Response: { data: { landfills[], total } }
```

### Flow: Web CRUD (Admin)

```
[Web: /tpa]
    ▼
[Web\LandfillController@index / create / store / edit / update / destroy / toggleStatus]
    │ CRUD lengkap untuk tabel landfills
    ▼
View: resources/views/landfills/
```

### File Terkait

| Layer | File |
|---|---|
| Mobile Service | `mobile-app/mobile/lib/services/public_service.dart` (baris 49-58) |
| Mobile Model | `mobile-app/mobile/lib/models/landfill.dart` |
| API Route | `web/laravel/routes/api.php` baris 49-50 |
| API Controller | `web/laravel/app/Http/Controllers/Api/LandfillApiController.php` |
| Web Route | `web/laravel/routes/web.php` baris 112-119 |
| Web Controller | `web/laravel/app/Http/Controllers/Web/LandfillController.php` |
| Model | `web/laravel/app/Models/Landfill.php` |
| View | `web/laravel/resources/views/landfills/` |
| Database | `landfills` |

---

## 13. Berita & Edukasi Lingkungan

**Deskripsi:** Konten berita dan artikel edukasi lingkungan yang dibuat admin dan dapat dibaca publik di mobile maupun web (tanpa login).

### Flow: Mobile (Baca)

```
[Mobile: citizen_home_tab.dart (daftar berita)]
    │ PublicService.getNews(search?, perPage=10)
    ▼
GET /api/news?per_page=10&search=?
    │
    ▼
[Api\NewsApiController@index]
    │ News::where('is_published', true)->orderByDesc('published_at')
    ▼
Response: { data: { articles[], total, ... } }

[Mobile: news_detail_screen.dart]
    │ PublicService.getNewsDetail(slug)
    ▼
GET /api/news/{slug}
    │
    ▼
[Api\NewsApiController@show]
    │ News::where('slug', slug)->where('is_published', true)->firstOrFail()
    ▼
Response: { data: article }
```

### Flow: Web CRUD (Admin)

```
[Web: /berita]
    ▼
[Web\NewsController@index / create / store / edit / update / destroy]
    │ CRUD berita dengan:
    │   - title, slug (auto-generate dari title)
    │   - content (rich text / textarea)
    │   - thumbnail image (upload + ImageStorageService)
    │   - is_published toggle, published_at
    │   - village scope: Super Admin => semua; Admin Desa => miliknya saja
    ▼
View: resources/views/news/index.blade.php  (dan create/edit/show)
```

### File Terkait

| Layer | File |
|---|---|
| Mobile UI | `mobile-app/mobile/lib/views/citizen/citizen_home_tab.dart` |
| Mobile UI | `mobile-app/mobile/lib/views/citizen/news_detail_screen.dart` |
| Mobile Service | `mobile-app/mobile/lib/services/public_service.dart` (baris 60-86) |
| Mobile Model | `mobile-app/mobile/lib/models/news_article.dart` |
| API Route | `web/laravel/routes/api.php` baris 53-54 |
| API Controller | `web/laravel/app/Http/Controllers/Api/NewsApiController.php` |
| Web Route | `web/laravel/routes/web.php` baris 121-128 |
| Web Controller | `web/laravel/app/Http/Controllers/Web/NewsController.php` |
| Model | `web/laravel/app/Models/News.php` |
| View | `web/laravel/resources/views/news/index.blade.php` |
| Database | `news` |

---

## 14. CRUD Petugas Kebersihan

**Deskripsi:** Admin (web) mengelola akun petugas kebersihan desa. Admin Kelurahan hanya bisa mengelola petugas di kelurahannya sendiri.

### Flow: Tambah Petugas

```
[Web: GET /petugas/create]
    ▼
[Web\WorkerController@create]
    │ Admin Desa: hanya tampil village sendiri
    │ Super Admin: tampil semua village
    ▼
View: resources/views/worker/create.blade.php
    │ (AJAX: GET /api/get-next-worker-email?village_id=&name=
    │   => auto-generate email preview saat nama diketik)

[POST /petugas]
    ▼
[Web\WorkerController@store]
    │ Jika email kosong => auto-generate:
    │    "{nama_depan}.{slug_kelurahan}{random_2digit}@papsampah.id"
    │    Loop hingga unique di tabel users
    │ Validate:
    │   name  => hanya huruf + spasi
    │   email => harus @papsampah.id, unique
    │   phone => 08xxx, 10-15 digit, unique
    │   password => min:8, confirmed
    │ User::create([role_id=petugas_desa, village_id, ...])
    ▼
Redirect ke /petugas dengan flash success
```

### Flow: Toggle Aktif/Nonaktif

```
[Web: PATCH /petugas/{id}/toggle-status]
    ▼
[WorkerController@toggleStatus]
    │ validateWorkerScope: admin_desa hanya village sendiri
    │ $worker->is_active = !$worker->is_active
    │ $worker->save()
    ▼
Redirect back
```

### File Terkait

| Layer | File |
|---|---|
| Web Route | `web/laravel/routes/web.php` baris 35-41 |
| Web Controller | `web/laravel/app/Http/Controllers/Web/WorkerController.php` |
| Model | `web/laravel/app/Models/User.php` |
| View | `web/laravel/resources/views/worker/` |
| Auto-Email Helper | Route inline `web/laravel/routes/web.php` baris 44-77 |
| Database | `users`, `roles`, `villages` |

---

## 15. CRUD Admin Kelurahan

**Deskripsi:** Super Admin mengelola akun Admin Kelurahan (satu admin per kelurahan).

### Flow

```
[Web: /akun-kelurahan]
    ▼
[Web\VillageAdminController@index / create / store / edit / update / toggleStatus]
    │ CRUD akun dengan role admin_desa
    │ Validasi: email, password, village_id (wajib, cek 1 admin per kelurahan)
    ▼
View: resources/views/village_admins/
```

### File Terkait

| Layer | File |
|---|---|
| Web Route | `web/laravel/routes/web.php` baris 80-85 |
| Web Controller | `web/laravel/app/Http/Controllers/Web/VillageAdminController.php` |
| Model | `web/laravel/app/Models/User.php` |
| View | `web/laravel/resources/views/village_admins/` |
| Database | `users`, `roles` |

---

## 16. Manajemen User Masyarakat

**Deskripsi:** Admin Web dapat melihat, mengedit, toggle status aktif/nonaktif, dan menghapus akun masyarakat.

### Flow

```
[Web: /user]
    ▼
[Web\UserController@index]
    │ User::with('role')->whereHas('role', name='masyarakat')
    │ Filter: search (name/email), status aktif/nonaktif
    ▼
View: resources/views/user/
```

### File Terkait

| Layer | File |
|---|---|
| Web Route | `web/laravel/routes/web.php` baris 87-92 |
| Web Controller | `web/laravel/app/Http/Controllers/Web/UserController.php` |
| View | `web/laravel/resources/views/user/` |
| Database | `users`, `roles` |

---

## 17. Upload & Kompresi Foto

**Deskripsi:** Semua foto yang diunggah (laporan warga & foto pembersihan petugas) diproses otomatis sebelum disimpan: koreksi orientasi EXIF, resize maks 1280px, dan kompresi JPEG 70%.

### Flow

```
[Mobile / Form Web: upload foto]
    │ File: JPEG / PNG / WEBP, max 10MB
    ▼
[ImageStorageService@processAndStore(file, folder)]
    │
    ├─ Cek file size <= 10MB
    ├─ imagecreatefromstring(rawContent)     => load ke memori GD
    ├─ autoOrientExif()                      => putar gambar sesuai EXIF orientation
    │    (penting untuk foto dari kamera mobile yang sering miring)
    ├─ Hitung resize: jika sisi terbesar > 1280px => scale proportional
    ├─ imagecopyresampled()                  => resize dengan resampling
    ├─ imagejpeg(quality=70)                 => compress ke JPEG
    │
    └─ Storage::disk('public')->put(
         key: "{folder}/{YYYY}/{MM}/img_{uniqid}_{random12}.jpg"
       )
    ▼
Return: { storage_key, mime_type, file_size, width, height, url }
```

### Konteks Penggunaan

| Konteks | Folder Simpan | Dipanggil dari |
|---|---|---|
| Laporan Sampah Warga | `reports/YYYY/MM/` | `WasteReportService@createReport` (baris 91) |
| Foto Pembersihan Petugas | `cleanup/YYYY/MM/` | `CleanupTaskService@uploadTaskPhoto` (baris 210) |

### File Terkait

| Layer | File |
|---|---|
| Service | `web/laravel/app/Services/ImageStorageService.php` |
| Storage Config | `web/laravel/config/filesystems.php` (disk: `public`) |
| Dipanggil oleh | `web/laravel/app/Services/WasteReportService.php` baris 91 |
| Dipanggil oleh | `web/laravel/app/Services/CleanupTaskService.php` baris 210 |

---

## STRUKTUR DATABASE (Ringkasan Tabel)

| Tabel | Keterangan |
|---|---|
| `roles` | 4 role: super_admin_kecamatan, admin_desa, masyarakat, petugas_desa |
| `users` | Semua pengguna (FK ke roles, villages) |
| `districts` | Data kecamatan |
| `villages` | 7 kelurahan Sumbersari + kolom `boundary` GEOMETRY (PostGIS polygon) |
| `waste_categories` | Kategori jenis sampah |
| `waste_reports` | Laporan sampah + kolom `location` GEOMETRY point (PostGIS) |
| `waste_report_photos` | Foto bukti laporan warga |
| `waste_report_status_histories` | Audit trail perubahan status laporan |
| `cleanup_tasks` | Tugas pembersihan (1 task per laporan) |
| `cleanup_task_workers` | Relasi petugas ↔ tugas (mendukung multi-worker per task) |
| `cleanup_task_photos` | Foto BEFORE / AFTER proses pembersihan |
| `notifications` | Notifikasi (model tersedia, integrasi UI masih partial) |
| `waste_banks` | Data bank sampah |
| `landfills` | Data TPA / TPS-3R |
| `news` | Berita & artikel edukasi lingkungan |
| `app_settings` | Konfigurasi app (contoh: `marker_display_days`) |
| `personal_access_tokens` | Token Sanctum untuk mobile app |

---

## ALUR KERJA END-TO-END (Ringkasan)

```
1. MASYARAKAT LAPOR (Mobile App)
   ──────────────────────────────
   Buka app > Login > Buat Laporan
   => Foto + GPS + Kategori + Deskripsi
   => API: POST /api/reports
   => PostGIS validasi lokasi dalam Sumbersari
   => Status laporan: PENDING_VALIDATION

2. ADMIN KELURAHAN VALIDASI (Web Dashboard)
   ──────────────────────────────────────────
   Login web > Lihat daftar laporan masuk
   => Setujui   > Status: VALIDATED
   => Tolak (+alasan) > Status: REJECTED

3. ADMIN KELURAHAN TUGASKAN (Web Dashboard)
   ──────────────────────────────────────────
   Pilih petugas (1 atau lebih) + instruksi kerja
   => Status laporan: ASSIGNED
   => CleanupTask dibuat / diperbarui
   => Setiap petugas: status PENDING

4. PETUGAS TERIMA TUGAS (Mobile App)
   ────────────────────────────────────
   Buka app > Login > Lihat daftar tugas
   => Terima Tugas  > Status: IN_PROGRESS
   => Upload foto BEFORE (dokumentasi awal)
   => Lakukan pembersihan di lapangan
   => Upload foto AFTER (wajib min 1)
   => Tandai Selesai > Status: PENDING_VERIFICATION
      (berlaku saat SEMUA petugas yang menerima tugas menyelesaikan)

5. ADMIN VERIFIKASI (Web Dashboard)
   ────────────────────────────────────
   Lihat foto BEFORE + AFTER petugas
   => Setujui  > Status laporan: RESOLVED (selesai)
   => Tolak    > Status: IN_PROGRESS (petugas kerja ulang)
```

---

*Dokumen dibuat berdasarkan analisis kode sumber project PAP Sampah — Politeknik Negeri Jember*
