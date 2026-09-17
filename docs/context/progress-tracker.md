# Progress Tracker

Update this file after every meaningful implementation change.

## Current Phase

- Fase 3 Selesai -> Siap masuk Fase 4 (Peta/Heatmap Kecamatan, Bank Sampah, TPA, Berita, & Integrasi Mobile)

## Current Goal

- Implementasi Peta Spasial & Heatmap Sebaran Sampah Kecamatan Sumbersari, data Bank Sampah/TPA/Berita, dan integrasi penuh aplikasi mobile Flutter.

## Completed

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
