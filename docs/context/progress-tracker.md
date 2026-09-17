# Progress Tracker

Update this file after every meaningful implementation change.

## Current Phase

- Fase 2 Selesai -> Siap masuk Fase 3 (Penugasan Petugas & Workflow Pembersihan)

## Current Goal

- Pembuatan Cleanup Task, penugasan multiple petugas desa oleh Admin Desa, dan integrasi API mobile Petugas untuk menerima/menolak/memulai pekerjaan.

## Completed

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
- [x] Verifikasi fungsional end-to-end (Submit laporan masyarakat -> PostGIS detect Sumbersari -> Web validasi Admin Desa -> Mobile fetch updated status) berhasil

## In Progress

- None currently.

## Next Up

- [ ] Web UI Pembuatan Cleanup Task & Penugasan Petugas Kebersihan oleh Admin Desa (Assign single / multiple workers)
- [ ] Mobile API Petugas Kebersihan: Daftar tugas pembersihan (`GET /api/tasks`), detail tugas, terima tugas (`POST /api/tasks/{id}/accept`), dan tolak tugas (`POST /api/tasks/{id}/reject` + alasan)
- [ ] Upload bukti progres dan foto hasil pembersihan oleh Petugas
- [ ] Web UI Verifikasi Hasil Pembersihan oleh Admin Desa (`RESOLVED`)
- [ ] Mobile Flutter UI integration

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
