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

- BCRYPT_ROUNDS: turunkan ke 10 untuk dev speed atau pertahankan 12?
- Database dev: setup Docker PostGIS lokal (`postgis/postgis:17-3.5`) untuk eliminasi network latency Sydney & support spatial tests?

## Architecture Decisions

- **Auth**: Laravel Sanctum — PAT untuk Flutter, session untuk web
- **Registrasi Petugas**: Dibuat dan didaftarkan oleh Admin Desa di dashboard website; Petugas login di mobile Flutter
- **Jalur integrasi**: Semua request Flutter wajib lewat REST API Laravel, tidak akses Supabase langsung
- **Object storage**: Supabase Storage (satu vendor dengan data platform)
- **Upload & Kompresi Foto**: Proxy lewat Laravel (Flutter multipart -> Laravel resize/compress -> Supabase Storage), JPEG, sisi terpanjang maks 1280px, kualitas ~70, batas upload 10MB, retensi permanen
- **Session driver**: file (bukan database) — lebih cepat untuk dev
- **Login fix**: eager load `role` sebelum `redirectByRole()` — eliminasi 1 lazy-load DB round trip ke remote Supabase (~80ms hemat)

## Session Notes

- Login lambat karena 3 faktor: bcrypt 12 rounds (~250ms), remote Supabase latency (~80ms/query), lazy load role (~80ms). Fix eager load sudah apply.
- Tests gagal karena PostGIS migration (MultiPolygon) jalan di SQLite in-memory — pre-existing issue, perlu PostgreSQL/PostGIS sungguhan.
- Design system ui-context: Tailwind v4 tokens via CDN config, Fraunces (serif) + Outfit (sans-serif), palette hijau-kuning-oranye-merah.
- Login page standalone (tidak extends layout). Dashboard extends layout.app.
- Rencana refactor folder: folder parent `laravel/laravel` akan distrukturkan menjadi `web/laravel`.
