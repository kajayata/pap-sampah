# Flow Fitur Utama Mobile Flutter — PAP Sampah

Dokumen ini menganalisis tiga fitur utama mobile berdasarkan `docs/FEATURE_FLOW.md` dan kode aktual:

1. Register masyarakat
2. Pelaporan sampah
3. Akses peta spasial

Fokus pembahasan: alur layar Flutter, state/provider, service HTTP, endpoint API, dan titik backend yang menerima request. Semua path relatif terhadap root repo.

**Sumber acuan:** `docs/FEATURE_FLOW.md` bagian 1, 4, dan 9.

---

## Ringkasan Fitur

| Fitur | Entry point mobile | Endpoint utama | Handler backend | Status implementasi |
|---|---|---|---|---|
| Register | `OnboardingScreen` / `LoginScreen` → `RegisterScreen` | `POST /api/auth/register` | `Api\AuthController@register` | Terimplementasi |
| Pelaporan sampah | FAB / aksi Beranda → `CreateReportScreen` | `POST /api/reports` | `Api\WasteReportController@store` | Terimplementasi |
| Akses peta | Tab `Peta` / aksi Beranda → `SpatialMapTab` | `GET /api/map/waste-points` | `Api\MapApiController@wastePoints` | Terimplementasi sebagian |

Catatan: fitur peta di mobile baru memuat titik laporan dan bank sampah. Heatmap dan batas wilayah sudah tersedia di API, tetapi belum dipakai layar mobile.

---

## 0. Fondasi Mobile

| Komponen | File | Baris | Fungsi |
|---|---|---|---|
| Provider global | `mobile-app/mobile/lib/main.dart` | 25-31 | Mendaftarkan `AuthProvider`, `ReportProvider`, `PublicProvider`, `WorkerTaskProvider`, `NotificationProvider` |
| API base URL | `mobile-app/mobile/lib/core/constants/api_constants.dart` | 4-18 | Default `http://127.0.0.1:8000/api`, bisa diubah lewat pengaturan login |
| Endpoint auth | `mobile-app/mobile/lib/core/constants/api_constants.dart` | 32-36 | `login`, `register`, `logout`, `me` |
| Endpoint laporan | `mobile-app/mobile/lib/core/constants/api_constants.dart` | 38-41 | `categories`, `reports`, `reportDetail` |
| Endpoint peta | `mobile-app/mobile/lib/core/constants/api_constants.dart` | 51-54 | `mapWastePoints`, `mapHeatmap`, `mapBoundaries` |
| HTTP client | `mobile-app/mobile/lib/core/network/api_client.dart` | 17-146 | GET, POST, PUT, DELETE, multipart; menyisipkan Bearer token |
| Penyimpanan token | `mobile-app/mobile/lib/core/storage/token_storage.dart` | 5-41 | Simpan/baca/hapus token dan user JSON di `SharedPreferences` |
| Routing awal | `mobile-app/mobile/lib/views/splash_screen.dart` | 42-67 | Cek token; ke onboarding, home masyarakat, atau home petugas |

Alur awal aplikasi:

```
SplashScreen
  └─ AuthProvider.checkAuthStatus()
      ├─ token valid   → CitizenMainScreen / WorkerMainScreen
      └─ token tidak ada → OnboardingScreen
                              ├─ Masuk  → LoginScreen
                              └─ Daftar → RegisterScreen
```

Referensi:

- `mobile-app/mobile/lib/views/splash_screen.dart:42-67` — cek status autentikasi dan redirect.
- `mobile-app/mobile/lib/views/auth/onboarding_screen.dart:77-98` — tombol Masuk dan Daftar Akun Baru.
- `mobile-app/mobile/lib/views/auth/login_screen.dart:239-245` — link menuju register dari halaman login.

---

## 1. Register Masyarakat

### 1.1 Tujuan

Membuat akun baru dengan role `masyarakat`, menyimpan token Sanctum, lalu langsung membuka halaman utama masyarakat.

### 1.2 Aktor dan entry point

| Entry | File | Baris |
|---|---|---|
| Tombol “Daftar Akun Baru” di onboarding | `mobile-app/mobile/lib/views/auth/onboarding_screen.dart` | 93-97 |
| Link “Daftar” di login | `mobile-app/mobile/lib/views/auth/login_screen.dart` | 239-245 |
| Layar form register | `mobile-app/mobile/lib/views/auth/register_screen.dart` | 8-165 |

### 1.3 Flow end-to-end

```
[Onboarding / Login]
    │ tekan Daftar
    ▼
[RegisterScreen]
    │ input: name, email, phone (opsional),
    │        password, password_confirmation
    │ validasi lokal via Form
    │ AuthProvider.register()
    ▼
[AuthProvider.register]
    │ status = authenticating
    │ AuthService.register()
    ▼
[AuthService.register]
    │ POST /api/auth/register
    │ body JSON
    ▼
[Api\AuthController@register]
    │ validasi: name, email unik, password min 8 + confirmed, phone opsional
    │ cari role masyarakat
    │ User::create([... is_active = true])
    │ createToken('auth-token')
    ▼
Response 201: { user, token, data }
    │
    ▼
[AuthService]
    │ User.fromJson()
    │ TokenStorage.saveAuth(token, user)
    ▼
[AuthProvider]
    │ currentUser = user
    │ status = authenticated
    │ notifyListeners()
    ▼
[RegisterScreen]
    │ pushAndRemoveUntil(CitizenMainScreen)
```

### 1.4 Detail langkah UI

| Langkah | File | Baris | Keterangan |
|---|---|---|---|
| Form key dan controller | `mobile-app/mobile/lib/views/auth/register_screen.dart` | 16-22 | Controller untuk nama, email, telepon, password, konfirmasi |
| Handler submit | `mobile-app/mobile/lib/views/auth/register_screen.dart` | 34-62 | Validasi form, panggil `AuthProvider.register`, redirect atau SnackBar error |
| Field nama | `mobile-app/mobile/lib/views/auth/register_screen.dart` | 88-98 | Wajib diisi |
| Field email | `mobile-app/mobile/lib/views/auth/register_screen.dart` | 100-112 | Wajib, harus mengandung `@` |
| Field telepon | `mobile-app/mobile/lib/views/auth/register_screen.dart` | 114-121 | Opsional |
| Field password | `mobile-app/mobile/lib/views/auth/register_screen.dart` | 123-136 | Minimal 8 karakter |
| Konfirmasi password | `mobile-app/mobile/lib/views/auth/register_screen.dart` | 138-152 | Harus sama dengan password |
| Tombol Daftar | `mobile-app/mobile/lib/views/auth/register_screen.dart` | 154 | Loading mengikuti status autentikasi |
| Navigasi sukses | `mobile-app/mobile/lib/views/auth/register_screen.dart` | 48-52 | `pushAndRemoveUntil` ke `CitizenMainScreen` |

### 1.5 State dan service mobile

| Layer | File | Baris | Fungsi |
|---|---|---|---|
| UI | `mobile-app/mobile/lib/views/auth/register_screen.dart` | 34-62 | Submit form dan tampilkan hasil |
| Provider | `mobile-app/mobile/lib/providers/auth_provider.dart` | 61-89 | Set status, panggil service, simpan error |
| Service | `mobile-app/mobile/lib/services/auth_service.dart` | 33-61 | POST register, parsing token/user, simpan auth |
| Storage | `mobile-app/mobile/lib/core/storage/token_storage.dart` | 9-13 | Simpan token dan user JSON |
| Model | `mobile-app/mobile/lib/models/user.dart` | 25-49 | Parsing `role`, `village`, `village_name` |
| HTTP | `mobile-app/mobile/lib/core/network/api_client.dart` | 72-87 | `ApiClient.post` dengan JSON dan header Bearer |

### 1.6 Kontrak API register

**Request**

```http
POST /api/auth/register
Content-Type: application/json
```

```json
{
  "name": "Nama Pengguna",
  "email": "nama@email.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "081234567890"
}
```

**Response sukses — 201**

```json
{
  "user": {
    "id": 1,
    "name": "Nama Pengguna",
    "email": "nama@email.com",
    "phone": "081234567890",
    "is_active": true,
    "role": { "id": 4, "name": "masyarakat" }
  },
  "token": "1|plain-text-sanctum-token",
  "data": {
    "user": { "...": "..." },
    "token": "1|plain-text-sanctum-token"
  }
}
```

**Response gagal — 422**

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["Email sudah terdaftar."],
    "password": ["Kata sandi minimal 8 karakter."]
  }
}
```

### 1.7 Backend register

| Layer | File | Baris | Fungsi |
|---|---|---|---|
| Route | `web/laravel/routes/api.php` | 19-25 | `POST /register` dan alias `POST /auth/register` |
| Controller | `web/laravel/app/Http/Controllers/Api/AuthController.php` | 19-65 | Validasi, assign role, create user, create token |
| Model user | `web/laravel/app/Models/User.php` | 12-47 | Relasi role/village dan hash password |
| Model role | `web/laravel/app/Models/Role.php` | 9-20 | Master role |
| Migrasi users | `web/laravel/database/migrations/2024_01_01_000005_create_users_table.php` | 25-46 | Kolom user, unique email, foreign key role/village |
| Migrasi roles | `web/laravel/database/migrations/2024_01_01_000001_create_roles_table.php` | 16-19 | Nama role unik |

Validasi backend:

- `name`: required, string, maksimal 255.
- `email`: required, format email, unik di tabel `users`.
- `password`: required, minimal 8, harus sama dengan `password_confirmation`.
- `phone`: nullable, string, maksimal 20.
- Role selalu `masyarakat`; pengguna tidak bisa memilih role dari mobile.

Referensi: `web/laravel/app/Http/Controllers/Api/AuthController.php:21-41`.

### 1.8 Edge case dan perilaku saat ini

- Register sukses langsung login karena token disimpan sebelum navigasi.
- Jika register gagal, `AuthProvider` mengubah status menjadi `unauthenticated` dan `RegisterScreen` menampilkan `auth.errorMessage` lewat SnackBar (`register_screen.dart:53-61`).
- Belum ada verifikasi email.
- Belum ada pemilihan kelurahan saat register; `village_id` user masyarakat tidak diisi oleh endpoint register.
- Setelah register, user diarahkan ke `CitizenMainScreen`, bukan kembali ke login.

### 1.9 Acceptance checklist

- [ ] Form menolak nama kosong, email tanpa `@`, password kurang dari 8 karakter, dan konfirmasi berbeda.
- [ ] Email duplikat menghasilkan pesan “Email sudah terdaftar.”
- [ ] Register sukses menyimpan token di `SharedPreferences`.
- [ ] Register sukses membuka `CitizenMainScreen`.
- [ ] Role user baru adalah `masyarakat`.
- [ ] Restart aplikasi tetap login karena token tersimpan.

---

## 2. Pelaporan Sampah

### 2.1 Tujuan

Masyarakat mengirim laporan sampah liar berupa satu foto, koordinat GPS, kategori, dan deskripsi opsional. Backend memvalidasi lokasi dengan PostGIS dan menyimpan laporan berstatus `PENDING_VALIDATION`.

### 2.2 Entry point

| Entry | File | Baris |
|---|---|---|
| FAB “Lapor Sampah” | `mobile-app/mobile/lib/views/citizen/citizen_main_screen.dart` | 189-196 |
| Handler buka form | `mobile-app/mobile/lib/views/citizen/citizen_main_screen.dart` | 54-62 |
| Aksi “Lapor Sampah” di Beranda | `mobile-app/mobile/lib/views/citizen/citizen_home_tab.dart` | 517-530 |
| Form laporan | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 10-451 |

### 2.3 Flow end-to-end

```
[CitizenMainScreen / CitizenHomeTab]
    │ buka CreateReportScreen
    ▼
[CreateReportScreen.initState]
    ├─ ReportProvider.loadCategories()
    │    └─ GET /api/categories
    └─ _getCurrentLocation()
         ├─ cek GPS aktif
         ├─ cek/request izin lokasi
         └─ Geolocator.getCurrentPosition()
    ▼
[Form laporan]
    ├─ 1. foto dari kamera/galeri
    ├─ 2. koordinat GPS
    ├─ 3. kategori sampah
    └─ 4. deskripsi opsional
    ▼
[_handleSubmit]
    ├─ validasi foto wajib
    ├─ validasi kategori wajib
    ├─ validasi koordinat wajib
    ├─ validasi form
    └─ ReportProvider.submitReport()
    ▼
[ReportProvider.submitReport]
    └─ ReportService.submitReport()
    ▼
[ReportService.submitReport]
    ├─ fields: category_id, latitude, longitude, description
    ├─ file: photos[]
    └─ ApiClient.multipartPost()
    ▼
POST /api/reports
    ▼
[Api\WasteReportController@store]
    ├─ StoreWasteReportRequest
    └─ WasteReportService.createReport()
         ├─ findVillageByCoordinates()
         │    └─ ST_Contains(boundary, point)
         ├─ generate report_code
         ├─ insert waste_reports (PENDING_VALIDATION)
         ├─ process & store foto
         ├─ insert waste_report_photos
         └─ insert waste_report_status_histories
    ▼
Response 201: { status, message, data }
    ▼
[CreateReportScreen]
    ├─ dialog “Laporan Terkirim!”
    ├─ tampilkan report_code
    └─ kembali ke tab Laporan
```

### 2.4 Detail langkah UI

| Langkah | File | Baris | Keterangan |
|---|---|---|---|
| Load kategori dan GPS saat layar dibuka | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 28-35 | `loadCategories()` dan `_getCurrentLocation()` |
| Ambil foto kamera/galeri | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 43-61 | `ImagePicker`, resize 1600px, quality 85 |
| Cek layanan GPS | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 70-77 | Jika GPS mati, submit tidak bisa |
| Minta izin lokasi | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 79-97 | Menangani denied dan deniedForever |
| Ambil koordinat | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 99-113 | Akurasi high, status koordinat ditampilkan |
| Validasi foto | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 116-125 | Foto wajib |
| Validasi kategori | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 127-135 | Kategori wajib |
| Validasi koordinat | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 137-145 | Koordinat wajib |
| Submit laporan | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 149-156 | Panggil provider |
| Dialog sukses | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 160-213 | Tampilkan kode laporan |
| SnackBar gagal | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 214-222 | Tampilkan error provider |
| Bagian foto | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 239-326 | Kamera atau galeri |
| Bagian GPS | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 329-388 | Deteksi/perbarui koordinat |
| Bagian kategori | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 390-411 | Dropdown kategori |
| Bagian deskripsi | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 413-425 | Maksimal 3 baris tampilan |
| Tombol kirim | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 428-444 | Loading saat submit |

### 2.5 State dan service mobile

| Layer | File | Baris | Fungsi |
|---|---|---|---|
| UI form | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 10-451 | Input foto, GPS, kategori, deskripsi |
| Provider | `mobile-app/mobile/lib/providers/report_provider.dart` | 22-27 | `loadCategories()` |
| Provider | `mobile-app/mobile/lib/providers/report_provider.dart` | 51-79 | `submitReport()`, insert laporan baru ke list |
| Service | `mobile-app/mobile/lib/services/report_service.dart` | 9-17 | `getCategories()` |
| Service | `mobile-app/mobile/lib/services/report_service.dart` | 46-76 | Multipart POST laporan |
| HTTP | `mobile-app/mobile/lib/core/network/api_client.dart` | 119-146 | `multipartPost()` |
| Model kategori | `mobile-app/mobile/lib/models/waste_category.dart` | 1-25 | Parsing kategori |
| Model laporan | `mobile-app/mobile/lib/models/waste_report.dart` | 50-160 | Parsing laporan, foto, status, riwayat |
| Daftar laporan | `mobile-app/mobile/lib/views/citizen/report_list_screen.dart` | 8-261 | Filter status, list laporan, navigasi detail |
| Detail laporan | `mobile-app/mobile/lib/views/citizen/report_detail_screen.dart` | 7-331 | Foto, koordinat, deskripsi, timeline status |

### 2.6 Kontrak API laporan

**Request**

```http
POST /api/reports
Authorization: Bearer <token>
Content-Type: multipart/form-data
```

| Field | Tipe | Wajib | Keterangan |
|---|---|---|---|
| `photos[]` | file | Ya | 1-5 file; JPEG/JPG/PNG/WEBP; maksimal 10MB per file |
| `category_id` | integer | Ya | Harus ada di `waste_categories` |
| `latitude` | numeric | Ya | -90 sampai 90 |
| `longitude` | numeric | Ya | -180 sampai 180 |
| `description` | string | Tidak | Maksimal 1000 karakter |

Catatan: mobile saat ini hanya mengirim satu foto (`photoFile` tunggal), sedangkan backend menerima sampai 5 foto.

**Response sukses — 201**

```json
{
  "status": "success",
  "message": "Laporan sampah berhasil dikirim dan menunggu validasi.",
  "data": {
    "id": 10,
    "report_code": "REP-20261030-ABCDE",
    "status": "PENDING_VALIDATION",
    "latitude": -8.1725,
    "longitude": 113.7188,
    "description": "Sampah di pinggir jalan",
    "category": { "id": 4, "name": "Liar / Campuran" },
    "village": { "id": 1, "name": "Sumbersari" },
    "photos": [{ "id": 1, "url": "..." }],
    "status_histories": [{ "status": "PENDING_VALIDATION" }]
  }
}
```

**Response gagal — 422**

```json
{
  "status": "error",
  "message": "Validasi formulir laporan gagal.",
  "errors": {
    "photos": ["Minimal sertakan 1 foto bukti kondisi sampah."],
    "latitude": ["Koordinat latitude wajib disertakan."]
  }
}
```

**Response di luar wilayah — 422**

```json
{
  "status": "error",
  "message": "Lokasi laporan berada di luar wilayah operasional Kecamatan Sumbersari.",
  "errors": {
    "location": ["Lokasi laporan berada di luar wilayah operasional Kecamatan Sumbersari."]
  }
}
```

### 2.7 Backend laporan

| Layer | File | Baris | Fungsi |
|---|---|---|---|
| Route list | `web/laravel/routes/api.php` | 74 | `GET /reports` |
| Route create | `web/laravel/routes/api.php` | 75 | `POST /reports` |
| Route detail | `web/laravel/routes/api.php` | 76 | `GET /reports/{id}` |
| Controller list | `web/laravel/app/Http/Controllers/Api/WasteReportController.php` | 22-46 | Laporan milik user login |
| Controller create | `web/laravel/app/Http/Controllers/Api/WasteReportController.php` | 51-82 | Panggil service, return 201 |
| Controller detail | `web/laravel/app/Http/Controllers/Api/WasteReportController.php` | 87-123 | Detail + cek ownership |
| Form request | `web/laravel/app/Http/Requests/StoreWasteReportRequest.php` | 16-25 | Aturan validasi |
| Pesan validasi | `web/laravel/app/Http/Requests/StoreWasteReportRequest.php` | 28-45 | Pesan error Bahasa Indonesia |
| Service cari wilayah | `web/laravel/app/Services/WasteReportService.php` | 26-32 | `ST_Contains` PostGIS |
| Service create | `web/laravel/app/Services/WasteReportService.php` | 43-117 | Transaksi insert laporan, foto, histori |
| Model laporan | `web/laravel/app/Models/WasteReport.php` | 12-176 | Status constants, relasi, scope koordinat |
| Scope koordinat | `web/laravel/app/Models/WasteReport.php` | 96-103 | `ST_Y`/`ST_X` dari kolom `location` |
| Migrasi laporan | `web/laravel/database/migrations/2024_01_01_000007_create_waste_reports_table.php` | 24-60 | Tabel laporan + kolom geometry + index GIST |
| Migrasi foto | `web/laravel/database/migrations/2024_01_01_000008_create_waste_report_photos_table.php` | 22-41 | Metadata foto + geometry |

### 2.8 Aturan backend

- Lokasi wajib berada di dalam boundary salah satu kelurahan aktif.
- Jika tidak ada boundary yang cocok, service melempar `ValidationException` dengan key `location`.
- Minimal satu foto wajib ada; request menolak array kosong.
- `report_code` dibuat dengan format `REP-YYYYMMDD-XXXXX` dan dicek unik.
- Insert laporan, foto, dan histori status dibungkus `DB::transaction`.
- Status awal selalu `PENDING_VALIDATION`.
- Foto diproses lewat `ImageStorageService::processAndStore()`: auto-orient EXIF, resize, kompresi JPEG.

### 2.9 Status laporan

```
PENDING_VALIDATION
    ├─ VALIDATED
    │     └─ ASSIGNED → IN_PROGRESS → PENDING_VERIFICATION → RESOLVED
    └─ REJECTED
```

Status ini ditampilkan di daftar dan detail laporan. Filter daftar laporan ada di `report_list_screen.dart:16-23`.

### 2.10 Edge case dan gap saat ini

- Mobile hanya mengirim satu foto; backend mendukung maksimal lima.
- Foto diambil dari kamera/galeri dengan resize 1600px dan quality 85, lalu backend mengompres lagi.
- Jika GPS tidak aktif atau izin ditolak, user tidak bisa submit karena koordinat wajib.
- Jika laporan di luar Sumbersari, error baru muncul dari backend, bukan validasi lokal.
- Deskripsi tidak wajib; foto, kategori, dan koordinat wajib.
- `ReportProvider.loadCategories()` menelan error tanpa pesan (`report_provider.dart:22-27`), sehingga dropdown bisa kosong tanpa penjelasan.

### 2.11 Acceptance checklist

- [ ] User tidak bisa submit tanpa foto.
- [ ] User tidak bisa submit tanpa kategori.
- [ ] User tidak bisa submit tanpa koordinat GPS.
- [ ] Request terkirim sebagai multipart ke `POST /api/reports`.
- [ ] Laporan baru muncul di tab Laporan setelah submit.
- [ ] Dialog sukses menampilkan `report_code`.
- [ ] Laporan di luar wilayah ditolak backend dengan pesan yang jelas.
- [ ] Status awal laporan adalah `PENDING_VALIDATION`.
- [ ] Detail laporan menampilkan foto, koordinat, deskripsi, dan timeline status.

---

## 3. Akses Peta Spasial

### 3.1 Tujuan

Menampilkan titik laporan sampah dan lokasi bank sampah di peta OpenStreetMap, dengan filter status dan tombol menuju lokasi pengguna.

### 3.2 Entry point

| Entry | File | Baris |
|---|---|---|
| Tab “Peta” di bottom navigation | `mobile-app/mobile/lib/views/citizen/citizen_main_screen.dart` | 197-203, 218-222 |
| Aksi “Peta Sebaran” di Beranda | `mobile-app/mobile/lib/views/citizen/citizen_home_tab.dart` | 534-544 |
| Layar peta | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 12-496 |

### 3.3 Flow end-to-end

```
[CitizenMainScreen]
    │ pilih tab index 1
    ▼
[SpatialMapTab.initState]
    ├─ PublicProvider.loadMapPoints()
    │    └─ GET /api/map/waste-points?type=all
    ├─ PublicProvider.loadWasteBanks()
    │    └─ GET /api/waste-banks
    └─ jika targetLat/targetLng tersedia → moveToCoordinates()
    ▼
[SpatialMapTab.build]
    ├─ buat marker laporan dari prov.mapPoints
    ├─ buat marker bank sampah dari prov.wasteBanks
    ├─ FlutterMap + OpenStreetMap TileLayer
    ├─ filter chips: Semua / Sampah Aktif / Selesai (H+7) / Bank Sampah
    └─ tombol refresh + my location
    ▼
Tap marker laporan
    └─ bottom sheet detail → ReportDetailScreen
Tap marker bank sampah
    └─ bottom sheet nama, alamat, telepon
```

### 3.4 Detail langkah UI

| Langkah | File | Baris | Keterangan |
|---|---|---|---|
| Inisialisasi peta | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 26-38 | Load titik laporan dan bank sampah |
| Pusat awal peta | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 23-24 | `LatLng(-8.1724, 113.7222)` |
| Pindah koordinat | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 40-42 | `MapController.move()` |
| Tombol lokasi saya | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 44-60 | Cek izin GPS dan pindah ke posisi saat ini |
| Marker laporan | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 69-101 | Warna merah untuk aktif, hijau untuk selesai |
| Marker bank sampah | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 104-138 | Hanya jika filter `all` atau `banks` |
| FlutterMap | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 144-159 | Tile OpenStreetMap, zoom 13.5, min 10, max 18 |
| Filter chips | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 161-204 | Semua, Sampah Aktif, Selesai (H+7), Bank Sampah |
| Tombol refresh | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 213-222 | Muat ulang titik dan bank |
| Tombol my location | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 224-230 | Pindah ke lokasi pengguna |
| Loading overlay | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 235-268 | “Memuat titik spasial...” |
| Bottom sheet laporan | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 320-419 | Status, kode, kategori, wilayah, deskripsi, tombol detail |
| Bottom sheet bank sampah | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 421-495 | Nama, alamat, telepon |

### 3.5 State dan service mobile

| Layer | File | Baris | Fungsi |
|---|---|---|---|
| UI peta | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 12-496 | Peta, marker, filter, bottom sheet |
| Provider | `mobile-app/mobile/lib/providers/public_provider.dart` | 57-73 | `loadMapPoints(filter)` |
| Provider | `mobile-app/mobile/lib/providers/public_provider.dart` | 80-94 | `loadWasteBanks()` |
| Provider | `mobile-app/mobile/lib/providers/public_provider.dart` | 142-150 | `refreshHomeData()` termasuk peta dan bank |
| Service | `mobile-app/mobile/lib/services/public_service.dart` | 11-27 | `getWastePoints()` |
| Service | `mobile-app/mobile/lib/services/public_service.dart` | 30-47 | `getWasteBanks()` |
| Model titik | `mobile-app/mobile/lib/models/waste_map_point.dart` | 35-52 | Parsing titik laporan |
| Model bank | `mobile-app/mobile/lib/models/waste_bank.dart` | 26-44 | Parsing bank sampah |
| Endpoint | `mobile-app/mobile/lib/core/constants/api_constants.dart` | 52-54 | URL map |

### 3.6 Kontrak API peta

**Titik laporan**

```http
GET /api/map/waste-points?type=all
GET /api/map/waste-points?type=active
GET /api/map/waste-points?type=resolved
GET /api/map/waste-points?village_id=1&type=all
```

Response:

```json
{
  "status": "success",
  "data": {
    "total_points": 1,
    "points": [
      {
        "id": 10,
        "report_code": "REP-20261030-ABCDE",
        "latitude": -8.1725,
        "longitude": 113.7188,
        "status": "PENDING_VALIDATION",
        "status_label": "Menunggu Validasi",
        "is_resolved": false,
        "category_name": "Liar / Campuran",
        "village_id": 1,
        "village_name": "Sumbersari",
        "description": "Sampah di pinggir jalan",
        "created_at": "2026-10-30T08:00:00+07:00",
        "created_at_formatted": "30 Okt 2026 08:00",
        "primary_photo_url": "...",
        "before_photos": [],
        "after_photos": []
      }
    ]
  }
}
```

**Bank sampah**

```http
GET /api/waste-banks
GET /api/waste-banks?village_id=1&search=sumbersari
```

**Heatmap dan batas wilayah — tersedia di API, belum dipakai mobile**

```http
GET /api/map/heatmap
GET /api/map/boundaries
```

### 3.7 Backend peta

| Layer | File | Baris | Fungsi |
|---|---|---|---|
| Route waste points | `web/laravel/routes/api.php` | 40 | `GET /map/waste-points` |
| Route heatmap | `web/laravel/routes/api.php` | 41 | `GET /map/heatmap` |
| Route boundaries | `web/laravel/routes/api.php` | 42 | `GET /map/boundaries` |
| Controller | `web/laravel/app/Http/Controllers/Api/MapApiController.php` | 22-36 | Validasi `type`, return points |
| Controller heatmap | `web/laravel/app/Http/Controllers/Api/MapApiController.php` | 42-54 | Return heatmap |
| Controller boundaries | `web/laravel/app/Http/Controllers/Api/MapApiController.php` | 59-68 | Return GeoJSON |
| Service points | `web/laravel/app/Services/MapService.php` | 20-104 | Query laporan + mapping response |
| Service heatmap | `web/laravel/app/Services/MapService.php` | 113-146 | Bobot intensitas per status |
| Service boundaries | `web/laravel/app/Services/MapService.php` | 154-184 | `ST_AsGeoJSON(boundary)` |
| Model scope | `web/laravel/app/Models/WasteReport.php` | 96-103 | Ambil lat/lng dari PostGIS |
| Pengaturan marker | `web/laravel/app/Services/MapService.php` | 22-23 | `marker_display_days`, default 7 hari |

Aturan tampil titik:

- `type=active`: status `PENDING_VALIDATION`, `VALIDATED`, `ASSIGNED`, `IN_PROGRESS`, `PENDING_VERIFICATION`.
- `type=resolved`: status `RESOLVED` dengan `resolved_at >= now - marker_display_days`.
- `type=all`: gabungan active dan resolved dalam window waktu.
- `type` tidak valid akan fallback ke `all` (`MapApiController.php:25`).
- Endpoint peta bersifat publik, tidak memakai middleware `auth:sanctum`.

### 3.8 Edge case dan gap saat ini

- Marker laporan selalu dibuat tanpa memfilter `mapFilter`; filter hanya memengaruhi request API. Saat memilih “Bank Sampah”, titik laporan lama masih bisa tampil.
- Filter “Bank Sampah” mengirim `type=banks` ke endpoint waste points. Backend tidak mengenal `banks`, lalu fallback ke `all`. Akibatnya filter bank sampah tidak benar-benar menyaring titik laporan.
- Heatmap dan boundary sudah tersedia di API dan `ApiConstants`, tetapi `PublicService` belum punya method untuk memanggilnya, dan `SpatialMapTab` belum menggambar keduanya.
- Izin lokasi untuk tombol “my location” hanya menangani `denied`; `deniedForever` tidak diberi pesan khusus.
- Tile peta memakai OpenStreetMap langsung tanpa cache/offline fallback.
- Bottom sheet laporan membuka `ReportDetailScreen`; untuk laporan milik orang lain, backend tetap mengizinkan role selain `masyarakat` dan membatasi masyarakat berdasarkan ownership.

### 3.9 Acceptance checklist

- [ ] Tab Peta terbuka dari bottom navigation dan aksi Beranda.
- [ ] Titik laporan tampil sesuai koordinat dari API.
- [ ] Marker merah untuk laporan aktif dan hijau untuk selesai.
- [ ] Tap marker laporan membuka bottom sheet dan detail laporan.
- [ ] Marker bank sampah tampil saat filter `all` atau `banks`.
- [ ] Tombol refresh memuat ulang titik dan bank.
- [ ] Tombol my location memindahkan peta ke posisi pengguna.
- [ ] Filter aktif/selesai mengirim `type` yang sesuai ke API.
- [ ] Peta tetap bisa dibuka tanpa login karena endpoint publik.

---

## 4. Analisis Singkat

### 4.1 Kekuatan implementasi

- Pemisahan lapisan mobile jelas: `views` → `providers` → `services` → `ApiClient`.
- Register memakai Sanctum dan token disimpan persisten; alur langsung ke home masyarakat.
- Pelaporan memakai multipart, validasi client dan server, transaksi DB, serta audit trail status.
- Peta memakai endpoint publik sehingga bisa diakses sebelum login.
- Backend peta sudah mendukung filter status, heatmap, dan GeoJSON boundary.

### 4.2 Risiko dan inkonsistensi

| Temuan | Lokasi | Dampak |
|---|---|---|
| Merge conflict marker masih ada | `mobile-app/mobile/lib/views/citizen/citizen_main_screen.dart:71`, `:184-185`; `mobile-app/mobile/lib/views/citizen/citizen_home_tab.dart:88`, `:123`, `:154` | Kode Dart tidak bisa dikompilasi sampai conflict diselesaikan |
| Filter “Bank Sampah” mengirim `type=banks` | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart:197-199` | Backend fallback ke `all`; filter tidak bekerja |
| Marker laporan tidak mengikuti filter | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart:69-101` | Titik laporan tetap tampil saat filter bank |
| Heatmap/boundary belum dipakai | `mobile-app/mobile/lib/services/public_service.dart:11-99` | Fitur peta mobile belum selengkap dokumen flow |
| Mobile hanya kirim satu foto | `mobile-app/mobile/lib/services/report_service.dart:46-69` | Backend mendukung 5 foto, fitur multi-foto belum terekspos |
| Error kategori ditelan | `mobile-app/mobile/lib/providers/report_provider.dart:22-27` | Dropdown kosong tanpa pesan saat API gagal |
| Register tidak mengisi `village_id` | `web/laravel/app/Http/Controllers/Api/AuthController.php:34-41` | User masyarakat tidak terikat kelurahan saat daftar |
| Peta tidak menangani `deniedForever` | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart:46-50` | Tombol lokasi gagal tanpa feedback jelas |

### 4.3 Rekomendasi prioritas

1. Selesaikan conflict marker di `citizen_main_screen.dart` dan `citizen_home_tab.dart` sebelum build.
2. Perbaiki filter peta: pisahkan filter bank dari `type` waste points, dan filter marker laporan berdasarkan `mapFilter`.
3. Tambahkan method `getHeatmap()` dan `getBoundaries()` di `PublicService` jika heatmap/boundary memang masuk scope mobile.
4. Dukung multi-foto di `CreateReportScreen` dan `ReportService` agar sesuai kapasitas backend.
5. Tampilkan error `loadCategories()` agar user tahu penyebab dropdown kosong.
6. Lengkapi penanganan izin lokasi `deniedForever` dengan arahan ke pengaturan aplikasi.

---

## 5. Traceability Matrix

| Fitur | Mobile | API | Backend | Database |
|---|---|---|---|---|
| Register | `register_screen.dart:34-62`, `auth_provider.dart:61-89`, `auth_service.dart:33-61` | `POST /api/auth/register` | `AuthController.php:19-65` | `users`, `roles`, `personal_access_tokens` |
| Pelaporan | `create_report_screen.dart:116-223`, `report_provider.dart:51-79`, `report_service.dart:46-76` | `POST /api/reports` | `WasteReportController.php:51-82`, `WasteReportService.php:43-117` | `waste_reports`, `waste_report_photos`, `waste_report_status_histories`, `villages` |
| Akses peta | `spatial_map_tab.dart:26-204`, `public_provider.dart:57-73`, `public_service.dart:11-27` | `GET /api/map/waste-points` | `MapApiController.php:22-36`, `MapService.php:20-104` | `waste_reports.location`, `villages.boundary`, `app_settings` |

---

## 6. Di Luar Cakupan Dokumen

Fitur berikut sengaja tidak dibahas detail karena bukan tiga fitur utama yang diminta:

- Login web admin.
- Validasi dan penolakan laporan oleh admin.
- Penugasan cleanup task.
- Tugas petugas mobile.
- Verifikasi hasil pembersihan.
- Bank sampah, TPA, berita, cuaca.
- CRUD admin dan petugas.
- Upload foto multi-file dan kompresi backend secara detail.
