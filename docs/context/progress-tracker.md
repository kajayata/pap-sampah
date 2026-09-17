# Progress Tracker

Update this file after every meaningful implementation change.

## Current Phase

- Fase 0 Selesai -> Siap masuk Fase 1 (Dashboard Data Real & Manajemen Petugas)

## Current Goal

- Wire dashboard stats cards to real data & build Petugas management (Admin Desa)

## Completed

- [x] Restrukturisasi direktori: `web/laravel/` selesai dan didaftarkan di Git
- [x] Instalasi dan konfigurasi `laravel/sanctum` untuk Personal Access Token (PAT)
- [x] Migrasi tabel `personal_access_tokens` dan `remember_token` di database
- [x] Trait `HasApiTokens` ditambahkan ke model `User`
- [x] Controller `App\Http\Controllers\Api\AuthController` diimplementasikan (register, login, logout, me) sesuai api-contract-auth.md
- [x] Seluruh 12 Model Eloquent domain dibuat (`WasteCategory`, `WasteReport`, `WasteReportPhoto`, `WasteReportStatusHistory`, `CleanupTask`, `CleanupTaskWorker`, `CleanupTaskPhoto`, `Notification`, `WasteBank`, `Landfill`, `News`, `AppSetting`)
- [x] Verifikasi fungsional API auth dan model via Tinker berhasil
- [x] Role & seed data (super_admin_kecamatan, admin_desa)
- [x] Layout updated: design tokens (colors, fonts, borderRadius), Google Fonts, fixed navbar
- [x] Login page: split layout, brand panel, form with icon inputs, error states
- [x] Dashboard page: stats cards, role-based layout, sidebar info, quick actions

## In Progress

- None currently.

## Next Up

- [ ] Wire dashboard stats to real data (count queries per role di web/laravel/app/Http/Controllers/Web/AuthController.php)
- [ ] Build CRUD Petugas Kebersihan Desa oleh Admin Desa (Web)
- [ ] Build laporan submit API (dengan PostGIS boundary validation) & Admin Desa validation workflow UI
- [ ] Build map/heatmap page
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
