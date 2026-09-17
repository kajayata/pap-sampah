# Architecture Context

## Tech Stack Versions (Finalized 2026-09-14)

| Komponen | Versi | Catatan |
|---|---|---|
| PHP | 8.3 | Kompatibel dengan Laravel 12 maupun 13; security support sampai Desember 2027 |
| Laravel | 12.x | **Keputusan sadar dengan trade-off diketahui**: bug-fix support Laravel 12 sudah berakhir 16 Agustus 2026 (per tanggal keputusan ini), tersisa security-fixes-only sampai Februari 2027. |
| PostgreSQL | 17 | Default terkini Supabase (platform maupun self-hosted), paritas dengan environment dev lokal |
| PostGIS | 3.5 (pinned di image Docker dev lokal: `postgis/postgis:17-3.5`) | Supabase mengelola versi PostGIS di sisi managed platform secara otomatis; versi 3.5 dipin khusus di container dev lokal supaya perilaku spatial query konsisten dengan production dan menghindari bug "works on my machine" |
| Flutter | 3.47.x (stable channel) | Versi stable terkini per awal September 2026; harus dikonfirmasi/diselaraskan dengan progres tim mobile yang sudah ada |

## Stack

| Layer | Technology | Role |
|---|---|---|
| Mobile | Flutter 3.47.x | Aplikasi mobile untuk Masyarakat dan Petugas dengan UI berbasis role |
| Website | Laravel 12.x (PHP 8.3) | Website public dan dashboard administrasi |
| Data platform | Supabase (PostgreSQL 17) | Platform terkelola untuk data PostgreSQL dan layanan Supabase yang dipakai project |
| Database | PostgreSQL 17 + PostGIS | Data relasional, relasi antar entity, serta data geografis |
| Storage | Supabase Storage | Menyimpan foto/media agar binary gambar tidak membebani database |
| Authentication | Laravel Sanctum | Personal Access Token untuk mobile, session/cookie untuk web dashboard |

## System Boundaries

- **Flutter mobile** — menangani UI masyarakat dan petugas, pengambilan foto, akses GPS, tampilan status, map, dan interaksi pekerjaan sesuai role.
- **Laravel website/application layer** — menangani website public, dashboard administrasi, aturan bisnis yang dibutuhkan sisi website, dan integrasi backend yang nantinya disepakati.
- **Supabase/PostgreSQL + PostGIS** — menjadi sumber data terstruktur dan data spasial. Relasi, wilayah, laporan, task, assignment, dan histori berada pada database.
- **Object/file storage** — menyimpan file foto aktual. Database hanya menyimpan metadata dan referensi file.
- **External weather provider** — menyediakan current weather; data cuaca tidak disimpan sebagai histori pada MVP.

## Storage Model

- **PostgreSQL + PostGIS**: menyimpan user, role, kecamatan, desa, kategori, laporan, status/history, cleanup task, assignment petugas, metadata foto, notifikasi, bank sampah, TPA, dan berita; data lokasi geospasial juga disimpan di sini.
- **Object/File Storage**: menyimpan file foto laporan dan foto pekerjaan pembersihan. Database menyimpan referensi file, MIME type, ukuran, dimensi, waktu pengambilan, dan uploader.
- **Media processing**: foto perlu dikompresi/di-resize sebelum atau saat disimpan agar penggunaan storage tetap hemat. Format hasil optimasi belum dikunci.
- **Weather**: current weather berasal dari provider eksternal dan tidak disimpan sebagai histori pada MVP.

## Auth and Access Model

- Mekanisme authentication belum ditentukan dan harus diputuskan sebelum implementasi authentication.
- Role sistem: Super Admin Kecamatan, Admin Desa, Petugas Desa, dan Masyarakat.
- Admin Desa memiliki scope pada satu desa. Petugas memiliki scope operasional pada desa masing-masing. Super Admin Kecamatan dapat melakukan monitoring seluruh desa dalam satu kecamatan.
- Masyarakat membuat dan melihat laporan miliknya, serta dapat melihat informasi publik yang memang ditujukan untuk umum.
- Hanya role yang berwenang yang dapat mengubah status laporan, membuat penugasan, atau melakukan verifikasi sesuai workflow.
- Scope wilayah harus ditegakkan di backend, bukan hanya dengan menyembunyikan fitur pada UI.

## Invariants

1. Sistem hanya menangani satu kecamatan; tidak boleh muncul scope pengelolaan seluruh Kabupaten Jember dalam desain MVP.
2. Setiap laporan memiliki satu desa sebagai lokasi administratif yang ditentukan dari koordinat laporan dan boundary desa. **Klarifikasi (Step 4, 2026-09-17):** bila koordinat berada di luar semua boundary desa terdaftar, submit laporan ditolak di application layer — tidak ada laporan tanpa desa yang tersimpan.
3. Satu laporan yang valid menghasilkan satu cleanup task pada workflow normal; satu cleanup task dapat dikerjakan oleh satu atau beberapa Petugas Desa. **Klarifikasi (Step 4, 2026-09-17):** task dianggap selesai hanya ketika seluruh petugas berstatus ACCEPTED juga menandai pekerjaannya completed.
4. Petugas hanya dapat menerima/menangani pekerjaan yang berada pada desa operasionalnya.
5. Petugas tidak dapat langsung menetapkan laporan sebagai `RESOLVED`; hasil pembersihan harus diverifikasi Admin Desa.
6. Laporan `RESOLVED` tidak dibuka kembali. Kejadian sampah baru membuat report baru.
7. `RESOLVED` langsung tidak dihitung sebagai sampah aktif pada heatmap, sedangkan mark selesai dapat tetap tampil sampai masa tampil marker berakhir.
8. Hilangnya mark dari peta tidak berarti penghapusan laporan, histori, atau foto dari storage.
9. Foto tidak disimpan sebagai binary besar di PostgreSQL; database menyimpan metadata/referensi dan file aktual berada di object/file storage.
10. Business rule dan authorization tidak boleh hanya bergantung pada client Flutter atau UI website.

## Finalized Architectural Decisions (2026-09-13)

- **Authentication:** Laravel Sanctum — Personal Access Token untuk Flutter mobile, session/cookie untuk web dashboard. Satu mekanisme untuk kedua konsumen, tanpa kompleksitas OAuth2 penuh (Passport) atau maintenance token custom (JWT) yang tidak dibutuhkan pada scope MVP ini.
- **Jalur integrasi:** Semua request terstruktur dari Flutter (laporan, status, assignment, dll) **wajib lewat REST API Laravel**. Flutter tidak mengakses service Supabase secara langsung untuk data terstruktur — ini menjaga invariant #10 (business rule/authorization tidak boleh hanya bergantung pada client) tetap ditegakkan di satu titik (Laravel), bukan terduplikasi di Supabase Row Level Security.
- **Object storage:** Dikunci ke **Supabase Storage**. Satu vendor dengan data platform yang sudah dipakai, mengurangi kompleksitas operasional untuk tim kecil di tahap MVP.

## Open Architectural Decisions

- Detail teknis upload foto (apakah lewat presigned URL langsung ke Supabase Storage dengan metadata tetap divalidasi Laravel, atau seluruhnya proxy lewat Laravel) belum diputuskan — akan dibahas saat merancang endpoint upload.
