# Progress Tracker

Update this file after every meaningful implementation change.

## Current Phase

- Fase 1 Selesai -> Siap masuk Fase 2 (Workflow Pelaporan Sampah & Validasi)

## Current Goal

- Service upload foto & kompresi, API submit laporan sampah (PostGIS check), dan UI validasi laporan (Admin Desa)

## Completed

- [x] Seeding data wilayah: Kecamatan Sumbersari & 7 Kelurahan (Antirogo, Karangrejo, Kebonsari, Kranjingan, Tegalgede, Wirolegi, Sumbersari) dengan MultiPolygon PostGIS
- [x] Akun resmi terdaftar: Super Admin Kecamatan (`kec.sumbersari@papsampah.id`) dan 7 Admin Kelurahan (`kel.(namadesa)@papsampah.id`)
- [x] Akun dummy lama (`admin@sukodono.id` dan `admin@kaliwates.id`) telah dibersihkan dari database
- [x] Wire dashboard statistik ke data nyata dengan optimasi 1 SQL agregat tunggal (`FILTER WHERE`)
- [x] Widget Laporan Terbaru dinamis per wilayah desa/kelurahan pada dashboard Admin Desa
- [x] Fitur CRUD Petugas Kebersihan Desa lengkap (`WorkerController`, form tambah, form edit, toggle aktif/nonaktif)
- [x] Navigasi navbar responsif terintegrasi dengan link Dashboard dan Petugas Kebersihan
- [x] Restrukturisasi direktori: `web/laravel/` selesai dan didaftarkan di Git
- [x] Instalasi dan konfigurasi `laravel/sanctum` untuk Personal Access Token (PAT)
- [x] Migrasi tabel `personal_access_tokens` dan `remember_token` di database
- [x] Trait `HasApiTokens` dan relasi domain ditambahkan ke model `User`
- [x] Controller `App\Http\Controllers\Api\AuthController` diimplementasikan (register, login, logout, me) sesuai api-contract-auth.md
- [x] Seluruh 12 Model Eloquent domain dibuat (`WasteCategory`, `WasteReport`, `WasteReportPhoto`, `WasteReportStatusHistory`, `CleanupTask`, `CleanupTaskWorker`, `CleanupTaskPhoto`, `Notification`, `WasteBank`, `Landfill`, `News`, `AppSetting`)
- [x] Verifikasi fungsional end-to-end (Admin Desa tambah petugas -> Petugas login mobile API) berhasil

## In Progress

- None currently.

## Next Up

- [ ] Service Upload & Kompresi Foto (Laravel Proxy ke Supabase Storage, JPEG 1280px ~70%)
- [ ] API Submit Laporan Masyarakat (`POST /api/reports`) dengan PostGIS boundary ST_Contains
- [ ] Web UI Validasi Laporan (Admin Desa approve / reject + alasan)
- [ ] Web UI Pembuatan Cleanup Task & Penugasan Petugas
- [ ] Mobile Flutter app

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
