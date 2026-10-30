# Alur Detail Fitur Utama Mobile — Register, Lapor Sampah, Peta

Dokumen ini menjelaskan alur teknis tiga fitur utama aplikasi mobile Flutter PAP Sampah:

1. Register masyarakat
2. Pelaporan sampah
3. Akses peta spasial

Fokus dokumen: aksi yang dilakukan, file yang dieksekusi, baris kode, dan logic di baliknya. Path file relatif terhadap root repo.

---

## Cara Membaca Dokumen

Setiap tabel memakai pola:

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Apa yang dilakukan user / sistem | File yang dijalankan | Rentang baris | Penjelasan proses |

Alur lengkap melewati empat lapisan:

```
Flutter View
    → Provider
        → Service
            → ApiClient
                → Laravel Route
                    → Controller
                        → Service / Model
                            → Database
```

---

## Fungsi Folder Utama Flutter

Bagian ini menjelaskan arti folder di `mobile-app/mobile/lib/` dengan bahasa sederhana.

- `lib/main.dart` — titik masuk aplikasi. Di sini aplikasi dibuat, tema dipasang, dan semua provider didaftarkan.
- `lib/views/` — berisi layar dan widget yang dilihat user. Contoh: halaman register, form lapor sampah, dan peta.
- `lib/providers/` — pengelola state. Provider menyimpan data sementara, mengatur status loading/error, lalu memberi tahu layar agar tampilan berubah.
- `lib/services/` — penghubung ke API. Service menyusun request, memanggil endpoint, dan mengubah response menjadi model.
- `lib/models/` — bentuk data di aplikasi. Contoh: `User`, `WasteReport`, `WasteMapPoint`.
- `lib/core/constants/` — nilai tetap seperti URL API.
- `lib/core/network/` — client HTTP dan penanganan error jaringan.
- `lib/core/storage/` — penyimpanan lokal, misalnya token login.
- `lib/core/theme/` — warna, font, dan gaya tampilan.
- `test/` — pengujian otomatis.
- `android/`, `ios/`, `web/`, `linux/`, `macos/`, `windows/` — konfigurasi platform. Jarang diubah untuk fitur biasa.

**Contoh peran provider:**

- `AuthProvider` menyimpan user yang sedang login, status login, pesan error, dan menyediakan fungsi `register`, `login`, serta `logout`.
- `ReportProvider` menyimpan daftar kategori, daftar laporan, status loading, dan menjalankan proses kirim laporan.
- `PublicProvider` menyimpan titik peta, bank sampah, berita, dan cuaca untuk halaman publik.

Sederhananya: **View menampilkan, Provider mengatur state, Service mengambil data, Model membentuk data.**

---

## Fungsi Folder Utama Laravel

Bagian ini menjelaskan folder penting di `web/laravel/`.

- `routes/` — daftar alamat URL. File `api.php` untuk mobile dan `web.php` untuk halaman admin.
- `app/Http/Controllers/` — penerima request. Controller membaca input, memanggil service, lalu mengembalikan response.
- `app/Http/Requests/` — aturan validasi request. Contoh: validasi foto, GPS, dan kategori laporan.
- `app/Services/` — aturan bisnis. Contoh: cek lokasi dengan PostGIS, simpan laporan, kompres foto.
- `app/Models/` — wakil tabel database di kode. Contoh: `User`, `WasteReport`, `Village`.
- `database/migrations/` — pembuat struktur tabel dan kolom.
- `database/seeders/` — pengisi data awal seperti role, admin, dan contoh data.
- `resources/views/` — tampilan web admin berbasis Blade.
- `config/` — konfigurasi aplikasi, database, dan penyimpanan file.
- `public/` — file yang bisa diakses browser dan titik masuk web.
- `storage/` — file upload, log, dan cache.
- `tests/` — pengujian otomatis.

Alur request Laravel secara sederhana:

```
Route → Controller → Request Validator → Service → Model → Database → Response
```

---

## 1. Register Masyarakat

### 1.1 Entry Point

| Aksi | File | Baris | Logic |
|---|---|---|---|
| User membuka aplikasi | `mobile-app/mobile/lib/views/splash_screen.dart` | 42-67 | Splash mengecek token. Jika belum login, user diarahkan ke `OnboardingScreen`. |
| User menekan “Daftar Akun Baru” | `mobile-app/mobile/lib/views/auth/onboarding_screen.dart` | 93-97 | `Navigator.push` membuka `RegisterScreen`. |
| User menekan “Daftar” dari login | `mobile-app/mobile/lib/views/auth/login_screen.dart` | 239-245 | `AuthSwitchLine` membuka `RegisterScreen`. |

### 1.2 Sequence Register

```
OnboardingScreen / LoginScreen
    │
    ▼
RegisterScreen
    │ isi form
    │ validasi lokal
    ▼
AuthProvider.register()
    │ status = authenticating
    ▼
AuthService.register()
    │ POST /api/auth/register
    ▼
Api\AuthController@register
    │ validasi request
    │ assign role masyarakat
    │ create user
    │ create Sanctum token
    ▼
Response 201 { user, token, data }
    │
    ▼
AuthService
    │ parse user
    │ simpan token + user
    ▼
AuthProvider
    │ status = authenticated
    ▼
RegisterScreen
    │ buka CitizenMainScreen
```

### 1.3 Form Register

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Membuat form dan controller | `mobile-app/mobile/lib/views/auth/register_screen.dart` | 16-22 | `GlobalKey<FormState>` dan lima `TextEditingController`: nama, email, telepon, password, konfirmasi. |
| Menampilkan field nama | `mobile-app/mobile/lib/views/auth/register_screen.dart` | 88-98 | Field wajib; validasi menolak nilai kosong. |
| Menampilkan field email | `mobile-app/mobile/lib/views/auth/register_screen.dart` | 100-112 | Field wajib; validasi lokal hanya mengecek keberadaan karakter `@`. |
| Menampilkan field telepon | `mobile-app/mobile/lib/views/auth/register_screen.dart` | 114-121 | Field opsional; dikirim hanya jika tidak kosong. |
| Menampilkan field password | `mobile-app/mobile/lib/views/auth/register_screen.dart` | 123-136 | Field wajib; minimal 8 karakter. |
| Menampilkan konfirmasi password | `mobile-app/mobile/lib/views/auth/register_screen.dart` | 138-152 | Nilai harus sama dengan field password. |
| Menekan tombol “Daftar” | `mobile-app/mobile/lib/views/auth/register_screen.dart` | 154 | Memanggil `_handleRegister()`. |

### 1.4 Aksi Submit Register

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Menjalankan submit | `mobile-app/mobile/lib/views/auth/register_screen.dart` | 34-62 | `_formKey.currentState!.validate()` dijalankan lebih dulu. Jika gagal, proses berhenti. |
| Mengambil input | `mobile-app/mobile/lib/views/auth/register_screen.dart` | 37-44 | Nama, email, telepon, password, dan konfirmasi diambil dari controller lalu dipangkas spasi. |
| Memanggil provider | `mobile-app/mobile/lib/views/auth/register_screen.dart` | 38-44 | `context.read<AuthProvider>().register(...)`. |
| Menangani sukses | `mobile-app/mobile/lib/views/auth/register_screen.dart` | 48-52 | `pushAndRemoveUntil` ke `CitizenMainScreen`; halaman register dihapus dari stack. |
| Menangani gagal | `mobile-app/mobile/lib/views/auth/register_screen.dart` | 53-61 | SnackBar menampilkan `auth.errorMessage`. |

### 1.5 State Provider

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Mengubah status ke loading | `mobile-app/mobile/lib/providers/auth_provider.dart` | 68-70 | `_status = authenticating`, error dikosongkan, `notifyListeners()`. |
| Memanggil service register | `mobile-app/mobile/lib/providers/auth_provider.dart` | 72-79 | Parameter diteruskan ke `AuthService.register()`. |
| Menyimpan hasil | `mobile-app/mobile/lib/providers/auth_provider.dart` | 80-82 | `_currentUser` diisi, status menjadi `authenticated`, widget rebuild. |
| Menangani error | `mobile-app/mobile/lib/providers/auth_provider.dart` | 83-88 | `_errorMessage` diisi, status kembali `unauthenticated`, return `false`. |

### 1.6 Service dan HTTP

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Mengirim request register | `mobile-app/mobile/lib/services/auth_service.dart` | 33-49 | Menyusun body JSON: `name`, `email`, `password`, `password_confirmation`, dan `phone` jika ada. |
| Memanggil endpoint | `mobile-app/mobile/lib/services/auth_service.dart` | 40-49 | `ApiClient.post(ApiConstants.register, body: ...)`. |
| Membaca response | `mobile-app/mobile/lib/services/auth_service.dart` | 51-58 | Response diambil dari `data` jika ada; token dan user divalidasi. |
| Menyimpan auth | `mobile-app/mobile/lib/services/auth_service.dart` | 56 | `TokenStorage.saveAuth(token: token, user: user)`. |
| Menyusun URL register | `mobile-app/mobile/lib/core/constants/api_constants.dart` | 34 | `$_baseUrl/auth/register`. |
| Menyisipkan header | `mobile-app/mobile/lib/core/network/api_client.dart` | 18-29 | Menambahkan `Accept`, `Content-Type: application/json`, dan Bearer token jika tersedia. |
| Mengirim POST | `mobile-app/mobile/lib/core/network/api_client.dart` | 72-87 | Body di-encode JSON, response diteruskan ke `_handleResponse`. |
| Menangani response HTTP | `mobile-app/mobile/lib/core/network/api_client.dart` | 32-57 | Status 2xx dianggap sukses; selain itu melempar `ApiException` dengan pesan backend. |

### 1.7 Penyimpanan Token

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Menyimpan token dan user | `mobile-app/mobile/lib/core/storage/token_storage.dart` | 9-13 | Token disimpan di key `auth_token`; user disimpan sebagai JSON di key `auth_user`. |
| Membaca token | `mobile-app/mobile/lib/core/storage/token_storage.dart` | 15-18 | `SharedPreferences.getString('auth_token')`. |
| Mengecek token | `mobile-app/mobile/lib/core/storage/token_storage.dart` | 38-41 | Token dianggap valid jika tidak null dan tidak kosong. |
| Menghapus auth | `mobile-app/mobile/lib/core/storage/token_storage.dart` | 32-36 | Menghapus token dan user saat logout. |

### 1.8 Backend Register

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Mencocokkan route | `web/laravel/routes/api.php` | 19-25 | `POST /register` dan alias `POST /auth/register` diarahkan ke `AuthController@register`. |
| Validasi request | `web/laravel/app/Http/Controllers/Api/AuthController.php` | 21-30 | `name` wajib; `email` wajib dan unik; `password` minimal 8 dan `confirmed`; `phone` opsional. |
| Mengambil role masyarakat | `web/laravel/app/Http/Controllers/Api/AuthController.php` | 32 | `Role::where('name', 'masyarakat')->firstOrFail()`. |
| Membuat user | `web/laravel/app/Http/Controllers/Api/AuthController.php` | 34-41 | `User::create()` dengan `role_id`, data user, dan `is_active = true`. |
| Hash password | `web/laravel/app/Models/User.php` | 31-37 | Cast `password => hashed` membuat password otomatis di-hash saat disimpan. |
| Membuat token | `web/laravel/app/Http/Controllers/Api/AuthController.php` | 43 | `createToken('auth-token')->plainTextToken`. |
| Menyusun response | `web/laravel/app/Http/Controllers/Api/AuthController.php` | 45-64 | Response 201 berisi `user`, `token`, dan `data`. |

### 1.9 Tabel Database Register

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Struktur tabel user | `web/laravel/database/migrations/2024_01_01_000005_create_users_table.php` | 25-46 | Kolom `role_id`, `village_id`, `name`, `email` unik, `password`, `phone`, `is_active`. |
| Struktur tabel role | `web/laravel/database/migrations/2024_01_01_000001_create_roles_table.php` | 16-19 | Kolom `id` dan `name` unik. |
| Relasi role | `web/laravel/app/Models/User.php` | 39-42 | `belongsTo(Role::class)`. |
| Relasi village | `web/laravel/app/Models/User.php` | 44-47 | `belongsTo(Village::class)`. |

### 1.10 Hasil Akhir Register

- User baru memiliki role `masyarakat`.
- Password disimpan dalam bentuk hash.
- Token Sanctum disimpan di perangkat.
- `AuthProvider.currentUser` terisi.
- Aplikasi membuka `CitizenMainScreen`.

---

## 2. Pelaporan Sampah

### 2.1 Entry Point

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Menekan FAB “Lapor Sampah” | `mobile-app/mobile/lib/views/citizen/citizen_main_screen.dart` | 189-196 | FAB memanggil `_openCreateReport()`. |
| Membuka form laporan | `mobile-app/mobile/lib/views/citizen/citizen_main_screen.dart` | 54-62 | `Navigator.push` ke `CreateReportScreen`; jika hasil `true`, pindah ke tab Laporan. |
| Menekan aksi “Lapor Sampah” di Beranda | `mobile-app/mobile/lib/views/citizen/citizen_home_tab.dart` | 517-530 | `Navigator.push` ke `CreateReportScreen`. |
| Layar form laporan | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 10-451 | Menampung foto, GPS, kategori, deskripsi, dan submit. |

### 2.2 Sequence Pelaporan

```
CreateReportScreen
    │ initState
    ├─ load kategori
    └─ ambil GPS
    │
    ▼
User mengisi form
    ├─ foto
    ├─ koordinat GPS
    ├─ kategori
    └─ deskripsi
    │
    ▼
_handleSubmit()
    │ validasi lokal
    ▼
ReportProvider.submitReport()
    ▼
ReportService.submitReport()
    │ multipart POST
    ▼
POST /api/reports
    ▼
WasteReportController@store
    ▼
StoreWasteReportRequest
    │ validasi field dan file
    ▼
WasteReportService.createReport()
    ├─ validasi wilayah PostGIS
    ├─ generate report_code
    ├─ insert waste_reports
    ├─ proses dan simpan foto
    ├─ insert waste_report_photos
    └─ insert status history
    ▼
Response 201
    ▼
Dialog sukses + kode laporan
```

### 2.3 Inisialisasi Layar

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Menjalankan init | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 28-35 | `addPostFrameCallback` memanggil `loadCategories()` dan `_getCurrentLocation()`. |
| Memuat kategori | `mobile-app/mobile/lib/providers/report_provider.dart` | 22-27 | `ReportService.getCategories()` mengisi `_categories` dan `notifyListeners()`. |
| Request kategori | `mobile-app/mobile/lib/services/report_service.dart` | 9-17 | `ApiClient.get(ApiConstants.categories)` dan parsing list. |
| URL kategori | `mobile-app/mobile/lib/core/constants/api_constants.dart` | 39 | `$_baseUrl/categories`. |
| Route kategori | `web/laravel/routes/api.php` | 37 | `GET /categories` ke `CategoryController@index`. |
| Controller kategori | `web/laravel/app/Http/Controllers/Api/CategoryController.php` | 14-22 | Mengambil `id`, `name`, `description` dari `WasteCategory`. |

### 2.4 Aksi Foto

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Membuka kamera | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 281-301 | `GestureDetector` memanggil `_pickImage(ImageSource.camera)`. |
| Membuka galeri | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 303-324 | `GestureDetector` memanggil `_pickImage(ImageSource.gallery)`. |
| Memilih foto | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 43-61 | `ImagePicker.pickImage()` dengan `maxWidth` dan `maxHeight` 1600, quality 85. |
| Menyimpan pilihan | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 51-53 | File disimpan di state `_selectedImage`. |
| Menampilkan preview | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 251-276 | `Image.file` dan tombol hapus foto. |

### 2.5 Aksi GPS

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Mulai deteksi lokasi | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 63-68 | State loading diaktifkan dan status “Mendeteksi koordinat GPS...” ditampilkan. |
| Cek layanan GPS | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 70-77 | `Geolocator.isLocationServiceEnabled()`; jika mati, proses berhenti. |
| Cek izin lokasi | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 79-89 | Jika `denied`, aplikasi meminta izin lewat `requestPermission()`. |
| Cek izin permanen | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 91-97 | Jika `deniedForever`, status izin ditolak permanen ditampilkan. |
| Ambil koordinat | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 99-101 | `Geolocator.getCurrentPosition()` dengan akurasi high. |
| Simpan posisi | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 103-107 | `_currentPosition` diisi; status koordinat ditampilkan dengan 5 desimal. |
| Tombol deteksi ulang | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 372-384 | Memanggil ulang `_getCurrentLocation()`. |

### 2.6 Aksi Kategori dan Deskripsi

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Menampilkan dropdown kategori | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 396-410 | Item berasal dari `ReportProvider.categories`; nilai disimpan di `_selectedCategory`. |
| Validasi kategori | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 409 | Kategori wajib dipilih. |
| Mengisi deskripsi | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 419-425 | `TextFormField` dengan maksimal 3 baris tampilan. |

### 2.7 Aksi Submit Laporan

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Mengecek foto | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 116-125 | Jika `_selectedImage == null`, SnackBar ditampilkan dan submit berhenti. |
| Mengecek kategori | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 127-135 | Jika `_selectedCategory == null`, submit berhenti. |
| Mengecek koordinat | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 137-145 | Jika `_currentPosition == null`, submit berhenti. |
| Validasi form | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 147 | `_formKey.currentState!.validate()`. |
| Memanggil provider | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 149-156 | `submitReport()` dengan foto, kategori, latitude, longitude, deskripsi. |
| Menampilkan sukses | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 160-213 | Dialog “Laporan Terkirim!” dan `report_code`. |
| Kembali ke daftar | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 203-210 | `Navigator.pop(context, true)`; layar utama pindah ke tab Laporan. |
| Menampilkan gagal | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 214-222 | SnackBar menampilkan `provider.errorMessage`. |

### 2.8 State Provider Laporan

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Menandai proses submit | `mobile-app/mobile/lib/providers/report_provider.dart` | 58-60 | `_isSubmitting = true`, error dikosongkan, widget rebuild. |
| Memanggil service | `mobile-app/mobile/lib/providers/report_provider.dart` | 62-69 | Parameter diteruskan ke `ReportService.submitReport()`. |
| Menambah laporan baru | `mobile-app/mobile/lib/providers/report_provider.dart` | 70-71 | Laporan baru dimasukkan ke urutan pertama `_reports`. |
| Menangani error | `mobile-app/mobile/lib/providers/report_provider.dart` | 72-74 | `_errorMessage` diisi dan method mengembalikan `null`. |
| Mengakhiri loading | `mobile-app/mobile/lib/providers/report_provider.dart` | 75-78 | `_isSubmitting = false`, `notifyListeners()`. |

### 2.9 Service Multipart

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Menyusun field | `mobile-app/mobile/lib/services/report_service.dart` | 53-58 | `category_id`, `latitude`, `longitude`, dan `description` jika tidak kosong. |
| Membuat file multipart | `mobile-app/mobile/lib/services/report_service.dart` | 60-63 | `MultipartFile.fromPath('photos[]', photoFile.path)`. |
| Mengirim laporan | `mobile-app/mobile/lib/services/report_service.dart` | 65-69 | `ApiClient.multipartPost()` ke `ApiConstants.reports`. |
| Membaca response | `mobile-app/mobile/lib/services/report_service.dart` | 71-75 | Jika `data` berupa Map, diubah menjadi `WasteReport`; jika tidak, melempar `ApiException`. |
| URL laporan | `mobile-app/mobile/lib/core/constants/api_constants.dart` | 40 | `$_baseUrl/reports`. |
| Multipart HTTP | `mobile-app/mobile/lib/core/network/api_client.dart` | 119-146 | Membuat `MultipartRequest`, menambahkan header auth, field, file, lalu mengirim. |

### 2.10 Backend Pelaporan

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Mencocokkan route | `web/laravel/routes/api.php` | 75 | `POST /reports` ke `WasteReportController@store`, di dalam middleware `auth:sanctum`. |
| Validasi request | `web/laravel/app/Http/Requests/StoreWasteReportRequest.php` | 16-25 | `photos` array 1-5 file gambar maksimal 10MB; `latitude`/`longitude` numerik; `category_id` valid; `description` maksimal 1000. |
| Pesan validasi | `web/laravel/app/Http/Requests/StoreWasteReportRequest.php` | 28-45 | Pesan error dalam Bahasa Indonesia. |
| Response validasi gagal | `web/laravel/app/Http/Requests/StoreWasteReportRequest.php` | 48-55 | Mengembalikan JSON 422 dengan `errors`. |
| Memanggil service | `web/laravel/app/Http/Controllers/Api/WasteReportController.php` | 51-58 | `createReport(reporter, data, photoFiles)`. |
| Mengambil ulang laporan | `web/laravel/app/Http/Controllers/Api/WasteReportController.php` | 60-63 | Relasi kategori, desa, foto, dan histori dimuat. |
| Response sukses | `web/laravel/app/Http/Controllers/Api/WasteReportController.php` | 65-69 | Status 201, pesan, dan data laporan. |
| Menangani error validasi | `web/laravel/app/Http/Controllers/Api/WasteReportController.php` | 70-75 | JSON 422 berisi `errors`. |
| Menangani error server | `web/laravel/app/Http/Controllers/Api/WasteReportController.php` | 76-81 | JSON 500 dengan pesan error. |

### 2.11 Logic Service Laporan

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Ambil koordinat | `web/laravel/app/Services/WasteReportService.php` | 45-46 | `latitude` dan `longitude` dikonversi ke float. |
| Cari wilayah | `web/laravel/app/Services/WasteReportService.php` | 49 | `findVillageByCoordinates()`. |
| Validasi wilayah | `web/laravel/app/Services/WasteReportService.php` | 50-54 | Jika tidak ada desa yang cocok, lempar `ValidationException` dengan key `location`. |
| Cek foto | `web/laravel/app/Services/WasteReportService.php` | 56-60 | Jika `photoFiles` kosong, lempar `ValidationException` dengan key `photos`. |
| Mulai transaksi | `web/laravel/app/Services/WasteReportService.php` | 62 | `DB::transaction()` membungkus seluruh proses insert. |
| Generate kode | `web/laravel/app/Services/WasteReportService.php` | 64-66 | Format `REP-YYYYMMDD-XXXXX`; `XXXXX` dari `Str::random(5)`. |
| Cek kode unik | `web/laravel/app/Services/WasteReportService.php` | 68-72 | Loop ulang jika `report_code` sudah ada. |
| Insert laporan | `web/laravel/app/Services/WasteReportService.php` | 75-85 | Insert ke `waste_reports`; status `PENDING_VALIDATION`; lokasi memakai `ST_SetSRID(ST_MakePoint(lon, lat), 4326)`. |
| Proses foto | `web/laravel/app/Services/WasteReportService.php` | 89-91 | Setiap file diproses lewat `ImageStorageService::processAndStore()`. |
| Insert metadata foto | `web/laravel/app/Services/WasteReportService.php` | 93-104 | Simpan `storage_key`, mime, ukuran, dimensi, waktu, uploader, dan lokasi. |
| Insert histori status | `web/laravel/app/Services/WasteReportService.php` | 107-113 | Status awal `PENDING_VALIDATION` dicatat sebagai audit trail. |
| Return laporan | `web/laravel/app/Services/WasteReportService.php` | 115 | Laporan dimuat dengan relasi kategori, desa, foto, dan histori. |

### 2.12 Logic Pencarian Wilayah

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Query PostGIS | `web/laravel/app/Services/WasteReportService.php` | 26-32 | `ST_Contains(boundary, ST_SetSRID(ST_MakePoint(lon, lat), 4326))`; hanya desa aktif. |
| Kolom boundary | `web/laravel/database/migrations/2024_01_01_000003_create_villages_table.php` | 30-31 | `boundary geometry(MultiPolygon, 4326)` dan index GIST. |

### 2.13 Logic Kompresi Foto

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Batas ukuran | `web/laravel/app/Services/ImageStorageService.php` | 12-14 | Maksimal sisi 1280px, kualitas JPEG 70, ukuran file 10MB. |
| Validasi ukuran | `web/laravel/app/Services/ImageStorageService.php` | 31-33 | File lebih dari 10MB ditolak. |
| Baca gambar | `web/laravel/app/Services/ImageStorageService.php` | 35-44 | `imagecreatefromstring()`; gagal berarti format tidak valid. |
| Auto-orient EXIF | `web/laravel/app/Services/ImageStorageService.php` | 46-47 | Orientasi kamera diperbaiki. |
| Hitung resize | `web/laravel/app/Services/ImageStorageService.php` | 49-61 | Jika sisi terpanjang > 1280, rasio dihitung agar aspek tetap. |
| Resample | `web/laravel/app/Services/ImageStorageService.php` | 63-82 | Gambar diproses ke ukuran target. |
| Kompres JPEG | `web/laravel/app/Services/ImageStorageService.php` | 84-87 | Output JPEG kualitas 70. |
| Simpan file | `web/laravel/app/Services/ImageStorageService.php` | 97-105 | Nama unik `folder/YYYY/MM/img_...jpg`, disimpan ke disk default. |
| Metadata | `web/laravel/app/Services/ImageStorageService.php` | 107-114 | Return `storage_key`, `mime_type`, `file_size`, `width`, `height`, `url`. |

### 2.14 Tabel Database Laporan

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Struktur laporan | `web/laravel/database/migrations/2024_01_01_000007_create_waste_reports_table.php` | 24-60 | Kolom laporan, status default `PENDING_VALIDATION`, `location geometry(Point, 4326)`, index GIST. |
| Check status | `web/laravel/database/migrations/2024_01_01_000007_create_waste_reports_table.php` | 52-55 | Status dibatasi ke tujuh nilai yang valid. |
| Struktur foto | `web/laravel/database/migrations/2024_01_01_000008_create_waste_report_photos_table.php` | 22-41 | Metadata foto, relasi ke laporan dan uploader, kolom `location` opsional. |
| Model foto | `web/laravel/app/Models/WasteReportPhoto.php` | 15-31 | `url` dihitung dari `storage_key`. |
| Model histori | `web/laravel/app/Models/WasteReportStatusHistory.php` | 15-27 | Menyimpan status, perubahan oleh siapa, dan catatan. |
| Scope koordinat | `web/laravel/app/Models/WasteReport.php` | 96-103 | `ST_Y(location)` dan `ST_X(location)` diekspos sebagai `latitude`/`longitude`. |
| Status awal | `web/laravel/app/Models/WasteReport.php` | 17 | Konstanta `STATUS_PENDING_VALIDATION`. |

### 2.15 Setelah Laporan Terkirim

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Form mengembalikan `true` | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` | 203-210 | Dialog ditutup dan halaman form ditutup. |
| Pindah tab laporan | `mobile-app/mobile/lib/views/citizen/citizen_main_screen.dart` | 57-60 | Callback `.then((created)` memanggil `_onTabSelected(2)`. |
| Memuat daftar laporan | `mobile-app/mobile/lib/views/citizen/report_list_screen.dart` | 25-31 | `ReportProvider.loadReports()` saat layar dibuka. |
| Filter status | `mobile-app/mobile/lib/views/citizen/report_list_screen.dart` | 16-23 | Tab: Semua, Menunggu, Tervalidasi, Diproses, Selesai, Ditolak. |
| Memilih filter | `mobile-app/mobile/lib/views/citizen/report_list_screen.dart` | 93-97 | `loadReports(status: ..., refresh: true)`. |
| Membuka detail | `mobile-app/mobile/lib/views/citizen/report_list_screen.dart` | 134-141 | Tap item membuka `ReportDetailScreen(reportId: rep.id)`. |
| Memuat detail | `mobile-app/mobile/lib/views/citizen/report_detail_screen.dart` | 27-45 | `ReportService.getReportDetail()` saat layar dibuka. |
| Service detail | `mobile-app/mobile/lib/services/report_service.dart` | 38-44 | `GET /reports/{id}` lalu parsing `WasteReport`. |
| Route detail | `web/laravel/routes/api.php` | 76 | `GET /reports/{id}` ke `WasteReportController@show`. |
| Controller detail | `web/laravel/app/Http/Controllers/Api/WasteReportController.php` | 87-123 | Memuat laporan dan relasi; cek ownership untuk role `masyarakat`. |

---

## 3. Akses Peta Spasial

### 3.1 Entry Point

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Memilih tab “Peta” | `mobile-app/mobile/lib/views/citizen/citizen_main_screen.dart` | 197-203 | `IndexedStack` menampilkan `SpatialMapTab` pada index 1. |
| Destinasi bottom navigation | `mobile-app/mobile/lib/views/citizen/citizen_main_screen.dart` | 218-222 | Label “Peta” memanggil `_onTabSelected(1)`. |
| Menekan aksi “Peta Sebaran” | `mobile-app/mobile/lib/views/citizen/citizen_home_tab.dart` | 534-544 | Memanggil `widget.onSwitchTab?.call(1)`. |
| Layar peta | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 12-496 | Menampilkan OpenStreetMap, marker, filter, dan detail. |

### 3.2 Sequence Peta

```
CitizenMainScreen
    │ pilih tab Peta
    ▼
SpatialMapTab.initState
    ├─ PublicProvider.loadMapPoints()
    │    └─ GET /api/map/waste-points
    ├─ PublicProvider.loadWasteBanks()
    │    └─ GET /api/waste-banks
    └─ jika ada target koordinat
         └─ moveToCoordinates()
    ▼
SpatialMapTab.build
    ├─ marker laporan
    ├─ marker bank sampah
    ├─ FlutterMap + OpenStreetMap
    ├─ filter chips
    └─ tombol refresh / my location
    ▼
User tap marker
    ├─ laporan → bottom sheet → detail laporan
    └─ bank sampah → bottom sheet info bank
```

### 3.3 Inisialisasi Peta

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Menyiapkan controller | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 22-24 | `MapController` dan pusat peta `LatLng(-8.1724, 113.7222)`. |
| Menjalankan init | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 26-38 | `addPostFrameCallback` memuat titik laporan dan bank sampah. |
| Memuat titik laporan | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 30-31 | `prov.loadMapPoints()`. |
| Memuat bank sampah | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 32 | `prov.loadWasteBanks()`. |
| Pindah ke target | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 34-36 | Jika `targetLat`/`targetLng` ada, panggil `moveToCoordinates()`. |
| Fungsi pindah | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 40-42 | `_mapController.move(LatLng(lat, lng), zoom)`. |

### 3.4 Memuat Titik Laporan

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Provider memulai load | `mobile-app/mobile/lib/providers/public_provider.dart` | 57-63 | Set filter jika diberikan, aktifkan `_isLoadingMap`, reset error. |
| Request titik | `mobile-app/mobile/lib/providers/public_provider.dart` | 65-66 | `PublicService.getWastePoints(type: _mapFilter)`. |
| Menyimpan hasil | `mobile-app/mobile/lib/providers/public_provider.dart` | 66 | `_mapPoints` diisi hasil service. |
| Menangani error | `mobile-app/mobile/lib/providers/public_provider.dart` | 67-69 | `_mapErrorMessage` diisi. |
| Menutup loading | `mobile-app/mobile/lib/providers/public_provider.dart` | 70-72 | `_isLoadingMap = false`, `notifyListeners()`. |
| Service menyusun query | `mobile-app/mobile/lib/services/public_service.dart` | 11-20 | Query `type` dan `village_id` opsional; URL dibentuk dari `ApiConstants.mapWastePoints`. |
| Parsing respons | `mobile-app/mobile/lib/services/public_service.dart` | 22-26 | `response['data']['points']` dipetakan ke `WasteMapPoint`. |
| URL titik | `mobile-app/mobile/lib/core/constants/api_constants.dart` | 52 | `$_baseUrl/map/waste-points`. |
| Route backend | `web/laravel/routes/api.php` | 40 | `GET /map/waste-points` ke `MapApiController@wastePoints`. |

### 3.5 Backend Titik Laporan

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Controller membaca query | `web/laravel/app/Http/Controllers/Api/MapApiController.php` | 22-25 | `village_id` opsional; `type` divalidasi ke `all`, `active`, atau `resolved`. |
| Memanggil service | `web/laravel/app/Http/Controllers/Api/MapApiController.php` | 27 | `MapService->getWastePoints($villageId, $type)`. |
| Response | `web/laravel/app/Http/Controllers/Api/MapApiController.php` | 29-35 | `total_points` dan array `points`. |
| Service membaca setting | `web/laravel/app/Services/MapService.php` | 22-23 | `marker_display_days`, default 7 hari. |
| Query dasar | `web/laravel/app/Services/MapService.php` | 25-33 | `WasteReport::withCoordinates()` dengan relasi kategori, desa, foto, dan foto AFTER. |
| Filter desa | `web/laravel/app/Services/MapService.php` | 35-37 | Jika `village_id` ada, query dibatasi. |
| Filter aktif | `web/laravel/app/Services/MapService.php` | 39-46 | Status aktif: pending, validated, assigned, in progress, pending verification. |
| Filter selesai | `web/laravel/app/Services/MapService.php` | 47-49 | Status `RESOLVED` dalam window `marker_display_days`. |
| Filter semua | `web/laravel/app/Services/MapService.php` | 50-63 | Gabungan laporan aktif dan selesai dalam window waktu. |
| Urutkan | `web/laravel/app/Services/MapService.php` | 66 | Urut `created_at` terbaru. |
| Mapping titik | `web/laravel/app/Services/MapService.php` | 68-103 | Menyusun `id`, kode, lat/lng, status, kategori, desa, deskripsi, waktu, foto before/after. |
| Ambil koordinat | `web/laravel/app/Models/WasteReport.php` | 96-103 | `ST_Y(location)` dan `ST_X(location)` sebagai `latitude`/`longitude`. |

### 3.6 Memuat Bank Sampah

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Provider memulai load | `mobile-app/mobile/lib/providers/public_provider.dart` | 80-85 | Aktifkan loading bank dan reset error. |
| Request bank | `mobile-app/mobile/lib/providers/public_provider.dart` | 87 | `PublicService.getWasteBanks(search: search)`. |
| Menyimpan hasil | `mobile-app/mobile/lib/providers/public_provider.dart` | 87 | `_wasteBanks` diisi hasil service. |
| Menutup loading | `mobile-app/mobile/lib/providers/public_provider.dart` | 88-93 | Error disimpan, loading dimatikan, rebuild. |
| Service menyusun query | `mobile-app/mobile/lib/services/public_service.dart` | 30-40 | Query `village_id` dan `search` opsional. |
| Parsing respons | `mobile-app/mobile/lib/services/public_service.dart` | 42-46 | `response['data']['waste_banks']` dipetakan ke `WasteBank`. |
| URL bank | `mobile-app/mobile/lib/core/constants/api_constants.dart` | 55 | `$_baseUrl/waste-banks`. |
| Route backend | `web/laravel/routes/api.php` | 45 | `GET /waste-banks` ke `WasteBankApiController@index`. |
| Controller bank | `web/laravel/app/Http/Controllers/Api/WasteBankApiController.php` | 18-44 | Filter `is_active`, `village_id`, dan `search`; return `waste_banks`. |
| Scope koordinat bank | `web/laravel/app/Models/WasteBank.php` | 52-56 | `ST_Y(location)` dan `ST_X(location)` sebagai `lat`/`lng`. |
| Model bank mobile | `mobile-app/mobile/lib/models/waste_bank.dart` | 26-44 | Parsing `id`, nama, alamat, telepon, koordinat, desa. |

### 3.7 Render Peta dan Marker

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Menyiapkan daftar marker | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 64-66 | `Consumer<PublicProvider>` dan list `Marker`. |
| Loop titik laporan | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 69-101 | Titik dengan lat/lng bukan 0 dibuat menjadi marker. |
| Warna marker laporan | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 78-95 | Merah untuk aktif, hijau untuk selesai; ikon tempat sampah atau centang. |
| Tap marker laporan | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 76-77 | Memanggil `_showWastePointDetails(pt)`. |
| Loop bank sampah | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 104-138 | Marker bank hanya ditambahkan jika filter `all` atau `banks`. |
| Warna marker bank | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 114-132 | Teal dengan ikon recycle. |
| Tap marker bank | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 112-113 | Memanggil `_showWasteBankDetails(bank)`. |
| Render FlutterMap | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 144-159 | `FlutterMap`, tile OpenStreetMap, `MarkerLayer`. |
| Tile OpenStreetMap | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 153-156 | URL tile `https://tile.openstreetmap.org/{z}/{x}/{y}.png`. |

### 3.8 Aksi Filter

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Chip “Semua” | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 170-174 | `loadMapPoints(filter: 'all')`. |
| Chip “Sampah Aktif” | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 176-182 | `loadMapPoints(filter: 'active')`. |
| Chip “Selesai (H+7)” | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 184-190 | `loadMapPoints(filter: 'resolved')`. |
| Chip “Bank Sampah” | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 192-200 | `loadMapPoints(filter: 'banks')`. |
| Menyimpan filter | `mobile-app/mobile/lib/providers/public_provider.dart` | 57-60 | Filter disimpan di `_mapFilter`. |
| Meneruskan filter | `mobile-app/mobile/lib/providers/public_provider.dart` | 66 | `_mapFilter` dikirim sebagai `type` ke service. |
| Style chip aktif | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 276-318 | Warna dan border berubah berdasarkan `isActive`. |

### 3.9 Aksi Lokasi Pengguna dan Refresh

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Tombol my location | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 224-230 | Memanggil `_moveToCurrentLocation()`. |
| Cek izin | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 44-50 | Cek izin; jika `denied`, minta izin. |
| Ambil posisi | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 51-52 | `Geolocator.getCurrentPosition()` lalu pindah dengan zoom 16. |
| Error lokasi | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 53-59 | SnackBar “Gagal membaca lokasi GPS”. |
| Tombol refresh | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 213-222 | Memuat ulang titik laporan dan bank sampah. |
| Overlay loading | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 235-268 | Tampil saat `prov.isLoadingMap` bernilai true. |

### 3.10 Aksi Detail Marker Laporan

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Membuka bottom sheet | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 320-326 | `showModalBottomSheet`. |
| Status visual | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 344-367 | Label status dan kode laporan. |
| Kategori dan wilayah | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 369-387 | Nama kategori, desa, dan waktu dibuat. |
| Deskripsi | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 388-396 | Ditampilkan maksimal 2 baris. |
| Buka detail laporan | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 398-413 | `Navigator.push` ke `ReportDetailScreen(reportId: pt.id)`. |
| Parsing titik | `mobile-app/mobile/lib/models/waste_map_point.dart` | 35-52 | Mengubah JSON menjadi `WasteMapPoint`. |

### 3.11 Aksi Detail Marker Bank

| Aksi | File | Baris | Logic |
|---|---|---|---|
| Membuka bottom sheet | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 421-427 | `showModalBottomSheet`. |
| Nama bank | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 445-463 | Ikon recycle dan nama bank. |
| Alamat | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 465-477 | Ditampilkan jika tersedia. |
| Telepon | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` | 478-489 | Ditampilkan jika tersedia. |

---

## 4. Ringkasan File yang Disentuh

### 4.1 Register

| Layer | File |
|---|---|
| UI | `mobile-app/mobile/lib/views/auth/onboarding_screen.dart` |
| UI | `mobile-app/mobile/lib/views/auth/login_screen.dart` |
| UI | `mobile-app/mobile/lib/views/auth/register_screen.dart` |
| State | `mobile-app/mobile/lib/providers/auth_provider.dart` |
| Service | `mobile-app/mobile/lib/services/auth_service.dart` |
| Storage | `mobile-app/mobile/lib/core/storage/token_storage.dart` |
| HTTP | `mobile-app/mobile/lib/core/network/api_client.dart` |
| API | `web/laravel/routes/api.php` |
| Controller | `web/laravel/app/Http/Controllers/Api/AuthController.php` |
| Model | `web/laravel/app/Models/User.php` |
| Database | `web/laravel/database/migrations/2024_01_01_000005_create_users_table.php` |

### 4.2 Pelaporan Sampah

| Layer | File |
|---|---|
| UI | `mobile-app/mobile/lib/views/citizen/create_report_screen.dart` |
| UI | `mobile-app/mobile/lib/views/citizen/report_list_screen.dart` |
| UI | `mobile-app/mobile/lib/views/citizen/report_detail_screen.dart` |
| State | `mobile-app/mobile/lib/providers/report_provider.dart` |
| Service | `mobile-app/mobile/lib/services/report_service.dart` |
| HTTP | `mobile-app/mobile/lib/core/network/api_client.dart` |
| API | `web/laravel/routes/api.php` |
| Controller | `web/laravel/app/Http/Controllers/Api/WasteReportController.php` |
| Validator | `web/laravel/app/Http/Requests/StoreWasteReportRequest.php` |
| Service | `web/laravel/app/Services/WasteReportService.php` |
| Service foto | `web/laravel/app/Services/ImageStorageService.php` |
| Model | `web/laravel/app/Models/WasteReport.php` |
| Database | `web/laravel/database/migrations/2024_01_01_000007_create_waste_reports_table.php` |
| Database | `web/laravel/database/migrations/2024_01_01_000008_create_waste_report_photos_table.php` |

### 4.3 Akses Peta

| Layer | File |
|---|---|
| UI | `mobile-app/mobile/lib/views/citizen/spatial_map_tab.dart` |
| UI navigasi | `mobile-app/mobile/lib/views/citizen/citizen_main_screen.dart` |
| UI Beranda | `mobile-app/mobile/lib/views/citizen/citizen_home_tab.dart` |
| State | `mobile-app/mobile/lib/providers/public_provider.dart` |
| Service | `mobile-app/mobile/lib/services/public_service.dart` |
| Model | `mobile-app/mobile/lib/models/waste_map_point.dart` |
| Model | `mobile-app/mobile/lib/models/waste_bank.dart` |
| API | `web/laravel/routes/api.php` |
| Controller | `web/laravel/app/Http/Controllers/Api/MapApiController.php` |
| Controller bank | `web/laravel/app/Http/Controllers/Api/WasteBankApiController.php` |
| Service | `web/laravel/app/Services/MapService.php` |
| Model | `web/laravel/app/Models/WasteReport.php` |
| Model | `web/laravel/app/Models/WasteBank.php` |
| Database | `web/laravel/database/migrations/2024_01_01_000007_create_waste_reports_table.php` |
| Database | `web/laravel/database/migrations/2024_01_01_000003_create_villages_table.php` |

---

## 5. Cara Cek API di Browser

Bagian ini untuk memastikan backend Laravel dan endpoint API berjalan sebelum aplikasi mobile diuji.

### 5.1 Jalankan backend

```bash
cd web/laravel
php artisan serve --host=0.0.0.0 --port=8000
```

Base URL API:

```text
http://127.0.0.1:8000/api
```

Base URL ini juga dipakai mobile di `mobile-app/mobile/lib/core/constants/api_constants.dart:5`.

### 5.2 Cek endpoint GET lewat address bar

Browser hanya bisa mengirim **GET** lewat address bar. Buka salah satu URL berikut:

```text
http://127.0.0.1:8000/api/categories
http://127.0.0.1:8000/api/map/waste-points
http://127.0.0.1:8000/api/map/heatmap
http://127.0.0.1:8000/api/waste-banks
http://127.0.0.1:8000/api/landfills
http://127.0.0.1:8000/api/news
http://127.0.0.1:8000/api/weather
http://127.0.0.1:8000/api/settings
```

Kalau berhasil, browser menampilkan JSON. Kalau gagal, perhatikan kode status:

- `200` — request berhasil.
- `401` — butuh token login.
- `404` — route atau data tidak ditemukan.
- `500` — ada error di backend; cek `storage/logs/laravel.log`.

### 5.3 Lihat daftar route API

```bash
cd web/laravel
php artisan route:list --path=api
```

Daftar route asli ada di `web/laravel/routes/api.php`.

### 5.4 Cek endpoint POST lewat DevTools browser

Buka `http://127.0.0.1:8000`, tekan `F12`, lalu buka tab **Console**.

Contoh login:

```js
const base = 'http://127.0.0.1:8000/api';

const res = await fetch(base + '/auth/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify({
    email: 'email@contoh.com',
    password: 'password123'
  })
});

const json = await res.json();
console.log(res.status, json);
```

Token login ada di `json.token`. Gunakan token itu untuk endpoint terproteksi:

```js
const token = json.token;

await fetch(base + '/me', {
  headers: {
    'Accept': 'application/json',
    'Authorization': 'Bearer ' + token
  }
}).then(r => r.json()).then(console.log);
```

Endpoint yang butuh token: `/me`, `/reports`, `/tasks`, dan `/logout`.

### 5.5 Cek upload laporan atau multipart

Address bar browser tidak bisa mengirim file. Gunakan **Postman**, **Hoppscotch**, atau **Thunder Client**.

```text
POST http://127.0.0.1:8000/api/reports
Authorization: Bearer <token>
Accept: application/json
Body: form-data
```

Field yang dikirim:

- `photos[]` — file gambar, minimal 1 dan maksimal 5.
- `category_id` — ID kategori sampah.
- `description` — deskripsi laporan, opsional.
- `latitude` — koordinat lintang.
- `longitude` — koordinat bujur.

Format foto: JPG, JPEG, PNG, atau WEBP. Ukuran maksimal 10 MB per foto.

### 5.6 Catatan

- Jangan tempel token login ke tempat publik.
- Jika memakai HP fisik, jalankan `adb reverse tcp:8000 tcp:8000`.
- Jika memakai emulator Android, base URL bisa diganti menjadi `http://10.0.2.2:8000/api`.
- Jika response berupa halaman HTML, tambahkan header `Accept: application/json`.

---

## 6. Tabel Database Penting

Tidak semua tabel dibahas. Hanya tabel yang dipakai langsung oleh Register, Pelaporan Sampah, dan Akses Peta.

| Tabel | Fungsi sederhana | Dipakai fitur |
|---|---|---|
| `users` | Menyimpan akun warga, petugas, dan admin. | Register, Pelaporan, Peta |
| `roles` | Menentukan jenis user, misalnya `masyarakat`. | Register |
| `villages` | Menyimpan data desa dan batas wilayah. | Register, Pelaporan, Peta |
| `waste_categories` | Menyimpan jenis sampah yang bisa dipilih. | Pelaporan |
| `waste_reports` | Menyimpan laporan: foto utama, lokasi, kategori, status, dan pelapor. | Pelaporan, Peta |
| `waste_report_photos` | Menyimpan foto tambahan laporan. | Pelaporan |
| `waste_report_status_histories` | Mencatat perubahan status laporan. | Pelaporan |
| `waste_banks` | Menyimpan lokasi bank sampah. | Peta |
| `personal_access_tokens` | Menyimpan token login Sanctum. | Register |

---

## 7. Ringkasan Logic per Fitur

| Fitur | Logic inti |
|---|---|
| Register | Form divalidasi lokal, dikirim JSON ke Sanctum, role dipaksa `masyarakat`, token disimpan, user diarahkan ke home. |
| Pelaporan | Foto + GPS + kategori + deskripsi dikirim multipart; backend cek wilayah dengan PostGIS, simpan laporan, kompres foto, catat histori status. |
| Peta | Mobile memuat titik laporan dan bank sampah; marker dibuat dari koordinat API; tap marker membuka detail; filter memicu request ulang. |
