# Progress Tracker — Pap Sampah

> Diperbarui setelah setiap unit kerja yang bermakna, sesuai ai-workflow-rules.md.
> Template lama (project sebelumnya) digantikan dengan versi ini karena isinya
> sudah tidak relevan. Kalau ada format khusus dari template lama yang ingin
> dipertahankan tim, sinkronkan manual ke sini.

## Status Saat Ini
-
## Unit Kerja Selesai
-
## Open Questions (belum jadi keputusan final)
-
## Keputusan Final (2026-09-13)

| Area | Keputusan | Alasan singkat |
|---|---|---|
| Authentication | Laravel Sanctum | Satu mekanisme untuk token mobile (Flutter) dan session web dashboard, tanpa kompleksitas OAuth2/JWT custom yang tidak dibutuhkan |
| Jalur integrasi | Semua request terstruktur Flutter lewat REST API Laravel, tidak akses langsung ke Supabase | Menjaga invariant #10 (authorization di satu titik, tidak terduplikasi di Supabase RLS) |
| Object storage | Supabase Storage (final, bukan kandidat lagi) | Satu vendor dengan data platform yang sudah dipakai, minim kompleksitas operasional untuk tim kecil |

## Keputusan Final (2026-09-17) — Versi Framework & Tooling

| Area | Keputusan | Alasan singkat |
|---|---|---|
| PHP | 8.3 | Kompatibel Laravel 12/13, security support sampai Des 2027 |
| Laravel | 12.x | Dipilih sadar meski bug-fix support sudah berakhir (16 Agu 2026); tersisa security-fixes-only sampai Feb 2027. Perlu waspada saat pilih package pihak ketiga yang butuh bug-fix aktif dari core. |
| PostgreSQL | 17 | Paritas dengan Supabase managed platform |
| PostGIS | 3.5 (pinned di Docker dev lokal) | Paritas spatial query behavior dengan production |

## Keputusan Final (2026-09-17) — Business Rules ERD (Step 4)

| Area | Keputusan | Alasan singkat |
|---|---|---|
| GPS di luar semua boundary desa | Tolak submit laporan; masyarakat diminta perbaiki lokasi | Konsisten dengan `waste_reports.village_id NOT NULL` — tidak perlu ubah skema, invariant #2 architecture.md tetap terjaga (setiap laporan pasti punya satu desa) |
| Penyelesaian cleanup_task dengan banyak petugas | Task selesai saat SEMUA petugas berstatus ACCEPTED juga menandai completed | Ditegakkan di service layer saat transisi `cleanup_tasks.status` ke COMPLETED; tidak perlu kolom baru |
| Penyimpanan alasan penolakan laporan (REJECTED) | Cukup di `waste_report_status_histories.note`, tanpa kolom tambahan di `waste_reports` | Menghindari duplikasi data — histori sudah mencatat status + note per perubahan |

**Kesimpulan Step 4:** ERD dinyatakan **final v1** — tidak ada perubahan skema dari draft migration yang sudah dibuat sebelumnya. Ketiga keputusan di atas murni business rule application-layer.

## Keputusan Final (2026-09-17) — Upload & Kompresi Foto

| Area | Keputusan | Alasan singkat |
|---|---|---|
| Jalur upload | Proxy lewat Laravel (Flutter upload multipart ke endpoint Laravel, Laravel yang push ke Supabase Storage) | Validasi & kompresi konsisten di satu tempat, sejalan dengan keputusan Step 1 (semua request lewat REST API Laravel). Bisa dimigrasikan ke presigned URL nanti tanpa mengubah API contract kalau volume naik. |
| Format hasil kompresi | JPEG | Kompatibilitas universal, rasio kompresi baik untuk foto natural |
| Resolusi maksimum | Sisi terpanjang **1280px** (lebih kecil dari rekomendasi awal 1920px, atas permintaan eksplisit — tidak perlu HD, yang penting masih jelas dilihat) | Cukup untuk verifikasi visual di map/dashboard, ukuran file jauh lebih kecil |
| Kualitas kompresi | JPEG quality ~70 | Titik keseimbangan ukuran file kecil vs foto tetap jelas dilihat |
| Batas ukuran upload asli (sebelum kompresi) | Maks 10MB per foto, ditolak di atas itu | Mencegah abuse upload file raw yang tidak wajar |
| Retention | Simpan permanen, tanpa auto-delete | Konsisten dengan invariant #8 (architecture.md) |

## Keputusan Teknis yang Sudah Diambil (bukan open question)

- PK memakai `INT` (`increments()`), bukan `BIGINT` default Laravel — sesuai code-standards.md.
- Kolom geometry PostGIS memakai raw `DB::statement()`, tidak menambah package spatial pihak ketiga (dependency minimal).
- Aturan "laporan wajib minimal 1 foto" ditegakkan di application layer (FormRequest + DB transaction), bukan DB constraint lintas tabel.
- `ON DELETE RESTRICT` untuk semua tabel bukti/histori (photos, status histories); `CASCADE` hanya untuk data operasional murni (cleanup_task_workers, notifications).
- Struktur repo: monorepo (`mobile/`, `laravel/`, `docs/context/`), migration mengikuti struktur bawaan Laravel (`laravel/database/migrations`).

## File yang Dilindungi (tidak disentuh workflow ini)

- `docs/context/ui-context.md` — dikelola tim mobile/UI terpisah. Belum ada di repo ini; jangan dibuat otomatis oleh workflow backend.

## Unit Kerja Berikutnya (usulan, belum dieksekusi)

1. Scaffold Laravel resmi oleh tim (`composer create-project laravel/laravel laravel`) — di luar kemampuan sandbox AI ini (tidak ada akses packagist.org).
2. Jalankan migration di environment nyata, verifikasi tidak ada konflik dengan migration bawaan Laravel (lihat `laravel/database/migrations/README.md`).
3. Model Eloquent + relasi untuk seluruh tabel di atas.
4. Keputusan open question #1 dan #2 (auth & integrasi) sebelum membangun endpoint API yang butuh otentikasi.
