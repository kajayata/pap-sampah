# Code Standards

## General

- Gunakan modul yang kecil dan memiliki satu tanggung jawab utama.
- Pisahkan concern UI, business logic, data access, dan integration service.
- Perbaiki akar masalah, bukan menumpuk workaround.
- Jangan mencampur perubahan yang tidak berkaitan dalam satu feature unit.
- Jangan membuat behavior baru yang belum ditentukan di context project.
- Nama entity, field, status, dan endpoint harus konsisten dengan domain Pap Sampah.

## Flutter / Dart

- Gunakan null-safety dan tipe data eksplisit.
- Hindari `dynamic` kecuali benar-benar diperlukan pada boundary data eksternal dan lakukan parsing/validasi setelahnya.
- Pisahkan presentation, state/business logic, dan data/service layer.
- Validasi input dari camera, GPS, form, dan response API sebelum dipakai oleh business logic.
- UI berdasarkan role tidak boleh menjadi satu-satunya lapisan keamanan; authorization tetap ditegakkan di backend.

## Laravel / PHP

- Gunakan controller yang tipis; business rule ditempatkan pada service/domain layer yang sesuai.
- Validasi request dilakukan sebelum business logic.
- Gunakan Policy/Gate atau mekanisme authorization yang sesuai untuk scope Super Admin Kecamatan, Admin Desa, Petugas Desa, dan Masyarakat.
- Route/API endpoint harus memiliki satu tanggung jawab yang jelas.
- Jangan mempercayai `village_id`, `report_id`, `worker_id`, atau status yang dikirim client tanpa validasi ownership/scope di server.
- Gunakan transaction untuk perubahan data yang harus berhasil sebagai satu unit, terutama validasi laporan, assignment task, dan penyelesaian workflow.

## Database

- Gunakan PostgreSQL + PostGIS sebagai sumber data relasional dan spasial.
- PK/FK harus konsisten dan menggunakan tipe integer yang sesuai kebutuhan; `BIGINT` hanya jika ada kebutuhan skala yang memang membenarkannya.
- Jangan menyimpan binary foto langsung di database.
- Gunakan geometry PostGIS untuk koordinat/boundary yang membutuhkan operasi spasial.
- Hindari menyimpan data turunan secara permanen bila dapat dihitung dari sumber data, misalnya heatmap intensity atau ranking.
- Setiap perubahan schema harus dilakukan melalui migration yang dapat diulang pada environment project.
- Business invariant yang tidak dapat dipaksakan oleh FK harus divalidasi pada application layer dan, bila tepat, diperkuat dengan database constraint.

## API and Integration

- Validasi dan parsing input harus dilakukan di system boundary sebelum data dipakai.
- Authentication dan authorization harus diperiksa sebelum mutation.
- Response API harus konsisten dan mudah diprediksi.
- Jangan menaruh credential provider eksternal di client atau source control.
- Integrasi weather provider harus melalui boundary/service tersendiri agar provider dapat diganti tanpa mengubah domain logic.
- Upload media harus menggunakan storage service, sedangkan database hanya menyimpan metadata/referensi.

## Data and Storage

- Metadata, ownership, relationship, status, dan lokasi terstruktur berada di PostgreSQL.
- Foto/media berukuran besar berada di object/file storage.
- Foto perlu dikompresi/di-resize agar hemat storage. **Dikunci (2026-09-17):** proxy upload lewat Laravel, output JPEG, sisi terpanjang maks 1280px, quality ~70, batas upload asli 10MB, retention permanen tanpa auto-delete.
- H+7 hanya merupakan aturan tampilan marker secara default; tidak boleh digunakan sebagai alasan menghapus report atau histori.

## File Organization

- `mobile/` atau struktur aplikasi Flutter — UI, state, service, dan model mobile.
- `web/laravel/` atau struktur aplikasi Laravel — website, HTTP layer, business logic, authorization, dan persistence integration.
- `docs/context/` — context project dan keputusan arsitektur; perubahan arsitektur/standards harus disinkronkan di sini.

## Documentation

- Dokumentasikan keputusan arsitektur yang sudah final.
- Keputusan yang belum final harus ditulis sebagai open question, bukan diasumsikan sebagai fakta.
- Gunakan bahasa Indonesia pada dokumentasi project, kecuali istilah teknis/kode yang memang lebih tepat dipertahankan dalam bahasa aslinya.
