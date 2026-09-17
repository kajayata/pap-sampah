# Progress Tracker

Update this file after every meaningful implementation change.

## Current Phase

- Fase 4 Selesai -> Siap masuk Integrasi Client Mobile Flutter (Masyarakat & Petugas)

## Current Goal

- Integrasi penuh REST API Laravel ke aplikasi mobile Flutter (`mobile-app/mobile/`) untuk fitur pelaporan warga, peta sampah, bank sampah, TPA, berita, cuaca, dan task management petugas kebersihan.

## Completed

- [x] Peta Spasial & Heatmap Sebaran Sampah (`MapService.php`, `MapApiController.php`, `MapWebController.php`, `map/index.blade.php`):
  - Penegakan Invariant #7: Laporan `RESOLVED` secara ketat tidak dihitung ke heatmap sampah aktif (hanya laporan aktif yang menjadi input heatmap)
  - Penegakan Invariant #8: Mark laporan selesai tetap tampil sementara di peta visualisasi selama periode H+7 (`app_settings.marker_display_days`), dan disembunyikan otomatis setelah masa tampil berakhir tanpa menghapus data laporan maupun foto
  - Modal komparasi visual foto Before (kondisi awal laporan warga) vs After (bukti pembersihan petugas) saat marker titik selesai diklik
  - Layer switch interaktif: Sampah Aktif (merah), Sampah Selesai H+7 (hijau), Heatmap Kepadatan (kuning-oranye-merah), Bank Sampah (teal), TPA/TPS-3R (indigo), dan Poligon Batas 7 Kelurahan Sumbersari
  - REST API Spasial: `GET /api/map/waste-points`, `GET /api/map/heatmap`, `GET /api/map/boundaries`
- [x] Master Data & REST API Publik Bank Sampah (`WasteBankApiController.php`, `WasteBankController.php`, `waste_banks/`):
  - Model `WasteBank` dengan accessor koordinat spasial PostGIS (`latitude`, `longitude`, `withCoordinates()`)
  - REST API: `GET /api/waste-banks` dan `GET /api/waste-banks/{id}` lengkap dengan relasi kelurahan dan kontak
  - Web Admin: CRUD Bank Sampah interaktif dengan picker koordinat Leaflet.js drag-and-drop
- [x] Master Data & REST API Publik TPA / TPS-3R (`LandfillApiController.php`, `LandfillController.php`, `landfills/`):
  - Model `Landfill` dengan accessor koordinat spasial PostGIS (`latitude`, `longitude`, `withCoordinates()`)
  - REST API: `GET /api/landfills` dan `GET /api/landfills/{id}`
  - Web Admin: CRUD Fasilitas TPA & TPS-3R dengan picker koordinat peta
- [x] Master Data & REST API Publik Berita Edukasi Lingkungan (`NewsApiController.php`, `NewsController.php`, `news/`):
  - Model `News` dengan accessor `thumbnail_url` via `ImageStorageService`, status `DRAFT`/`PUBLISHED`/`ARCHIVED`, dan auto-slug generator
  - REST API: `GET /api/news` (pagination, search, scope published) dan `GET /api/news/{slug}`
  - Web Admin: CRUD Berita edukasi lengkap dengan upload thumbnail kompresi
- [x] REST API Cuaca Terkini & Konfigurasi Publik (`WeatherService.php`, `PublicInfoApiController.php`):
  - `GET /api/weather`: Integrasi cuaca real-time Open-Meteo untuk Kecamatan Sumbersari (-8.172, 113.715) dengan caching 30 menit dan fallback tropical weather
  - `GET /api/settings`: Pengaturan konfigurasi publik (`marker_display_days: 7`, dll)
- [x] Seeder Data Master Fase 4 (`Fase4MasterDataSeeder.php`):
  - 7 Bank Sampah nyata di 7 kelurahan Sumbersari dengan koordinat PostGIS akurat
  - 2 Fasilitas TPA/TPS-3R rujukan
  - 3 Artikel berita edukasi pengelolaan dan pemilahan sampah
- [x] Automated Feature Test Suite Fase 4 (`Fase4FeatureTest.php`):
  - 100% lulus (20 tests passed, 125 assertions) mencakup seluruh endpoint publik spasial dan web controller
- [x] Integrasi Navigasi Navbar Web: Link "Peta & Heatmap", "Bank Sampah", "TPA", dan "Berita" responsif di header admin dashboard

- [x] Service Penugasan & Pembersihan (`CleanupTaskService.php`):
  - Penugasan multi-petugas kebersihan desa dengan catatan instruksi kerja & daftar alat/barang yang perlu dibawa (Invariant #3 & #4)
  - Penegakan Invariant #4: Petugas hanya dapat ditugaskan pada laporan di wilayah kelurahan operasionalnya
  - Penanganan respon tugas: Menerima tugas (`acceptTask`) dan menolak tugas dengan alasan wajib (`rejectTask`)
  - Upload foto bukti before/after pekerjaan pembersihan (`uploadTaskPhoto`)
  - Penegakan Invariant #3: Tugas hanya berpindah ke `PENDING_VERIFICATION` bila seluruh petugas berstatus `ACCEPTED` telah menyelesaikan pekerjaannya (`completeTaskByWorker`) dengan minimal 1 foto `AFTER`
  - Verifikasi hasil pembersihan oleh Admin Desa (`verifyAndResolve`) mengubah status laporan menjadi `RESOLVED` (Invariant #5 & #6)
  - Permintaan pembersihan ulang (`rejectVerification`) mengembalikan status ke `IN_PROGRESS`
- [x] Mobile REST API Petugas Kebersihan (`WorkerTaskController.php`):
  - `GET /api/tasks`: Daftar tugas pembersihan petugas yang login
  - `GET /api/tasks/{id}`: Detail tugas lengkap (laporan, foto, instruksi admin, rekan tim, foto before/after)
  - `POST /api/tasks/{id}/accept`: Menerima tugas pembersihan
  - `POST /api/tasks/{id}/reject`: Menolak tugas dengan alasan wajib
  - `POST /api/tasks/{id}/photos`: Upload foto bukti pembersihan (BEFORE / AFTER)
  - `POST /api/tasks/{id}/complete`: Menandai tugas selesai dari sisi petugas
- [x] Web Dashboard Penugasan & Verifikasi (`ReportController.php` & `reports/show.blade.php`):
  - Modal Form Penugasan: Checklist pemilihan multi-petugas kelurahan aktif & textarea instruksi/perlengkapan kerja admin
  - Card Pemantauan Tim Pembersihan: Menampilkan status masing-masing petugas (PENDING, ACCEPTED, REJECTED + alasan, COMPLETED)
  - Galeri Perbandingan Bukti Foto: Tab perbandingan foto kondisi awal laporan warga vs foto hasil pembersihan petugas (BEFORE & AFTER)
  - Panel Aksi Verifikasi: Tombol persetujuan final (RESOLVED) dan tombol permintaan pembersihan ulang
  - Fix Hak Akses & UX CTA: Role Super Admin tidak menampilkan CTA penugasan (hanya monitoring/info), label diringkas menjadi "Tugaskan Petugas", dan penambahan `@stack('scripts')` di app layout sehingga interaksi modal & script berjalan lancar.
- [x] Service Upload & Kompresi Foto (`ImageStorageService.php`): auto-orient EXIF kamera, resize proporsional sisi terpanjang maks 1280px, kompresi JPEG quality 70%, batas 10MB, penyimpanan ke storage disk dengan pencatatan metadata file
- [x] API Kategori Sampah (`GET /api/categories`) untuk pilihan pelaporan di mobile Flutter
- [x] API Submit Laporan Masyarakat (`POST /api/reports`) dengan validasi PostGIS `ST_Contains` ke 7 kelurahan Sumbersari (Invariant #2: jika di luar wilayah Sumbersari otomatis ditolak HTTP 422)
- [x] API Riwayat Laporan Masyarakat (`GET /api/reports`) dan detail laporan (`GET /api/reports/{id}`) dengan pagination, URL foto publik, dan audit trail status
- [x] Accessor spasial `latitude`, `longitude`, `status_label` dan scope `withCoordinates()` pada model `WasteReport`
- [x] Web Controller Laporan (`ReportController.php`): Filter status tabs, filter kelurahan (khusus Super Admin), pencarian, dan pagination
- [x] Web UI Daftar Laporan (`reports/index.blade.php`): Card laporan dengan thumbnail foto, badge status, info pelapor, koordinat, dan filter dinamis
- [x] Web UI Detail Laporan (`reports/show.blade.php`): Galeri foto dengan modal zoom, peta interaktif Leaflet.js dengan boundary polygon kelurahan, timeline status, dan modal aksi validasi/penolakan
- [x] Web Aksi Validasi Laporan: Admin Kelurahan menyetujui laporan (`POST /laporan/{id}/validate`) -> status berubah ke `VALIDATED`
- [x] Web Aksi Penolakan Laporan: Admin Kelurahan menolak laporan (`POST /laporan/{id}/reject`) dengan catatan alasan wajib -> status berubah ke `REJECTED`
- [x] Proteksi Otorisasi Wilayah: Admin Kelurahan A tidak dapat melihat/memvalidasi laporan milik Kelurahan B (HTTP 403)
- [x] Integrasi Navigasi & Dashboard: Menu "Laporan Sampah" di navbar dan integrasi link cepat pada widget laporan dashboard
- [x] Setup database dev lokal Docker PostGIS 3.5 pada PostgreSQL 17 (latensi turun dari ~100ms remote Sydney menjadi ~3ms)
- [x] Seeding data wilayah: Kecamatan Sumbersari & 7 Kelurahan (Antirogo, Karangrejo, Kebonsari, Kranjingan, Tegalgede, Wirolegi, Sumbersari) dengan MultiPolygon PostGIS
- [x] Akun resmi terdaftar: Super Admin Kecamatan (`kec.sumbersari@papsampah.id`) dan 7 Admin Kelurahan (`kel.(namadesa)@papsampah.id`)
- [x] Akun dummy lama (`admin@sukodono.id` dan `admin@kaliwates.id`) telah dibersihkan dari database
- [x] Wire dashboard statistik ke data nyata dengan optimasi 1 SQL agregat tunggal (`FILTER WHERE`)
- [x] Widget Laporan Terbaru dinamis per wilayah desa/kelurahan pada dashboard Admin Desa
- [x] Fitur CRUD Petugas Kebersihan Desa lengkap (`WorkerController`, form tambah, form edit, toggle aktif/nonaktif)
- [x] Navigasi navbar responsif terintegrasi dengan link Dashboard dan Petugas Kebersihan
- [x] Instalasi dan konfigurasi `laravel/sanctum` untuk Personal Access Token (PAT)
- [x] Migrasi tabel `personal_access_tokens` dan `remember_token` di database
- [x] Trait `HasApiTokens` dan relasi domain ditambahkan ke model `User`
- [x] Controller `App\Http\Controllers\Api\AuthController` diimplementasikan (register, login, logout, me) sesuai api-contract-auth.md
- [x] Seluruh 12 Model Eloquent domain dibuat (`WasteCategory`, `WasteReport`, `WasteReportPhoto`, `WasteReportStatusHistory`, `CleanupTask`, `CleanupTaskWorker`, `CleanupTaskPhoto`, `Notification`, `WasteBank`, `Landfill`, `News`, `AppSetting`)
- [x] Verifikasi fungsional end-to-end lengkap (Warga buat laporan -> Admin validasi & tugaskan petugas -> Petugas terima & upload bukti after -> Admin verifikasi RESOLVED) berhasil 100%

## In Progress

- None currently.

## Next Up

- [ ] Peta Spasial & Heatmap Kecamatan Sumbersari (Titik sampah aktif vs selesai H+7)
- [ ] Master Data & API Publik: Bank Sampah, TPA, Berita, & Cuaca
- [ ] Dashboard Monitoring Super Admin Kecamatan
- [ ] Integrasi Layar Aplikasi Mobile Flutter (Masyarakat & Petugas)

## Open Questions

- (Semua pertanyaan operasional database dev telah terselesaikan dengan setup Docker PostGIS lokal).

## Architecture Decisions

- **Database Dev**: PostgreSQL 17 + PostGIS 3.5 lokal via Docker Compose (`postgis/postgis:17-3.5`), latensi ~3ms per query (menggantikan latensi ~100ms remote Sydney saat development). Kredensial remote Supabase tetap tersimpan di `.env` (commented) untuk staging/production.
- **BCRYPT_ROUNDS**: 10 pada local dev untuk responsivitas login/register instan.
- **Auth**: Laravel Sanctum — PAT untuk Flutter, session untuk web
- **Registrasi Petugas**: Dibuat dan didaftarkan oleh Admin Desa di dashboard website; Petugas login di mobile Flutter
- **Jalur integrasi**: Semua request Flutter wajib lewat REST API Laravel, tidak akses Supabase langsung
- **Object storage**: Supabase Storage (satu vendor dengan data platform)
- **Upload & Kompresi Foto**: Proxy lewat Laravel (Flutter multipart -> Laravel resize/compress -> Supabase Storage), JPEG, sisi terpanjang maks 1280px, kualitas ~70, batas upload 10MB, retensi permanen
- **Session driver**: file (bukan database) — lebih cepat untuk dev
- **Login fix**: eager load `role` sebelum `redirectByRole()` — eliminasi 1 lazy-load DB round trip

## Session Notes

- Database lokal berjalan di container `papsampah-postgis` port 5432.
- Waktu eksekusi query turun dari ~100ms menjadi ~3ms (peningkatan kecepatan hingga 30-50x).
- `DatabaseSeeder` telah diperbarui memanggil `RoleSeeder`, `DistrictVillageSeeder`, `AdminSeeder`, dan `SampleWasteDataSeeder` secara berurutan.
- Login dan CRUD Petugas sekarang berjalan secepat kilat (sub-250ms total response time).
