# Ringkasan Perubahan CRUD Bank Sampah dan TPA/TPS-3R

Dokumen ini merangkum perubahan yang dibuat selama sesi ini untuk pengelolaan data Bank Sampah dan fasilitas TPA/TPS-3R.

## Perubahan Fitur

- Nomor telepon Bank Sampah dibatasi maksimal 13 digit pada form dan validasi server. Batas kolom database juga diubah menjadi 13 karakter.
- Data duplikat ditolak saat tambah maupun edit jika kelurahan, nama, dan alamat sama. Pencocokan nama dan alamat mengabaikan perbedaan huruf besar/kecil dan spasi tepi. Pesan yang ditampilkan: `Data sudah ada sebelumnya`.
- Pesan error dari proses simpan ditampilkan sebagai alert pada layout admin.
- Ditambahkan aksi hapus pada tabel Bank Sampah dan TPA/TPS-3R, lengkap dengan konfirmasi sebelum menghapus.
- Admin kelurahan hanya dapat menghapus Bank Sampah di kelurahannya sendiri; Super Admin Kecamatan dapat menghapus semua data Bank Sampah.
- Pencarian Bank Sampah dapat mencocokkan nama, alamat, nomor telepon, deskripsi, dan nama kelurahan. Kata kunci `aktif` dan `nonaktif` juga dapat digunakan untuk mencari berdasarkan status.

## File yang Diubah

- `web/laravel/app/Http/Controllers/Web/WasteBankController.php`
  Menambahkan validasi telepon, pemeriksaan duplikat saat tambah/edit, pencarian pada lebih banyak kolom, dan aksi hapus dengan pembatasan berdasarkan kelurahan.
- `web/laravel/app/Http/Controllers/Web/LandfillController.php`
  Menambahkan pemeriksaan duplikat saat tambah/edit dan aksi hapus untuk fasilitas TPA/TPS-3R.
- `web/laravel/routes/web.php`
  Menambahkan route `DELETE` untuk Bank Sampah dan TPA/TPS-3R.
- `web/laravel/resources/views/waste_banks/form.blade.php`
  Membatasi input nomor telepon menjadi maksimal 13 digit.
- `web/laravel/resources/views/waste_banks/index.blade.php`
  Memperbarui petunjuk pencarian dan menambahkan tombol Hapus dengan konfirmasi.
- `web/laravel/resources/views/landfills/index.blade.php`
  Menambahkan tombol Hapus dengan konfirmasi pada tabel fasilitas.
- `web/laravel/resources/views/layouts/app.blade.php`
  Menampilkan pesan error sesi sebagai alert.
- `web/laravel/tests/Feature/Fase4FeatureTest.php`
  Menambahkan test untuk duplikat, batas nomor telepon, aksi hapus, otorisasi hapus, serta pencarian melalui telepon dan deskripsi.

## File yang Ditambahkan

- `web/laravel/database/migrations/2026_09_30_000001_limit_waste_bank_phone_to_13_characters.php`
  Migration untuk mengubah panjang kolom `waste_banks.phone` menjadi 13 karakter. Migration ini sudah diterapkan pada database lokal.

## Verifikasi

Perintah `php artisan test --filter=Fase4FeatureTest` dijalankan dari `web/laravel` dan lulus dengan **17 test dan 147 assertion**.