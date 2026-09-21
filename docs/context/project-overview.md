# Pap Sampah

## Overview

Pap Sampah adalah sistem pelaporan dan penanganan sampah liar yang menghubungkan aplikasi mobile Flutter dengan website Laravel untuk satu wilayah kecamatan. Masyarakat menggunakan aplikasi mobile untuk melaporkan sampah dengan foto dan lokasi GPS, Admin Desa memvalidasi laporan serta menugaskan petugas kebersihan desa, dan petugas menangani laporan lalu mengirim bukti hasil pembersihan. Super Admin Kecamatan memantau kegiatan seluruh desa. Sistem juga menyediakan peta kondisi sampah, informasi bank sampah dan TPA, berita, FAQ, serta informasi cuaca saat ini.

## Goals

1. Membuat proses pelaporan sampah liar berbasis foto dan lokasi yang mudah digunakan masyarakat.
2. Membuat proses penanganan sampah yang terstruktur dari validasi laporan, penugasan beberapa petugas, pembersihan, hingga verifikasi hasil oleh Admin Desa.
3. Menyediakan monitoring kondisi sampah dan aktivitas penanganan pada satu kecamatan melalui peta, status laporan, dan statistik.

## Core User Flow

1. Masyarakat login/register pada aplikasi Flutter.
2. Masyarakat membuat laporan dengan foto sampah, lokasi GPS, kategori, dan deskripsi.
3. Sistem menentukan desa berdasarkan lokasi laporan.
4. Admin Desa menerima dan memvalidasi laporan.
5. Jika valid, Admin Desa membuat satu pekerjaan pembersihan dan menugaskan satu atau beberapa Petugas Desa.
6. Petugas menerima atau menolak penugasan. Jika menerima, petugas melakukan pembersihan.
7. Petugas mengirim foto hasil pembersihan beserta data pendukung yang tersedia.
8. Admin Desa memverifikasi hasil pembersihan.
9. Jika hasil valid, laporan menjadi `RESOLVED`.
10. Laporan yang sudah `RESOLVED` tidak dibuka kembali; kejadian sampah baru dibuat sebagai laporan baru.
11. Laporan selesai langsung tidak lagi dihitung sebagai sampah aktif pada heatmap, tetapi mark selesai tetap dapat ditampilkan sampai masa tampil marker berakhir, dengan default H+7.
12. Masyarakat dapat melihat status dan bukti foto before/after yang sudah diverifikasi.

## Features

### Pelaporan dan Penanganan Sampah

- Login/register untuk Masyarakat dan login untuk Petugas pada satu aplikasi Flutter dengan tampilan berdasarkan role.
- Pelaporan sampah menggunakan foto sebagai bukti dan akses lokasi GPS.
- Validasi laporan oleh Admin Desa.
- Penugasan satu pekerjaan pembersihan kepada satu atau beberapa Petugas Desa.
- Petugas dapat menerima atau menolak penugasan; penolakan memiliki alasan.
- Foto hasil pembersihan dikirim petugas dan diverifikasi Admin Desa.
- Status laporan dan histori proses penanganan.
- Notifikasi untuk peristiwa penting dalam workflow.

### Peta dan Informasi Wilayah

- Website menampilkan satu peta kecamatan yang menjadi scope aplikasi.
- Mark sampah muncul otomatis untuk laporan yang valid/aktif.
- Mark yang sudah selesai tetap dapat tampil sementara sebagai mark selesai dan menggunakan warna yang sesuai dengan visualisasi kondisi/heatmap.
- Saat mark dibuka, masyarakat dapat melihat foto before dan after yang telah diverifikasi.
- Mark selesai disembunyikan setelah masa tampil marker berakhir, default H+7; data laporan tidak dihapus.
- Penentuan desa laporan berasal dari koordinat GPS dan batas wilayah desa.
- Mark bank sampah dan TPA.
- Informasi current weather tanpa menyimpan histori cuaca.

### Website dan Monitoring

- Public website untuk informasi umum, peta, cara penggunaan aplikasi, FAQ, berita, dan fitur tambahan yang direncanakan.
- Dashboard Super Admin Kecamatan untuk monitoring seluruh desa dalam satu kecamatan.
- Dashboard Admin Desa untuk validasi, penugasan, monitoring pekerjaan, dan verifikasi hasil.
- Statistik operasional dan monitoring berbasis laporan.

### Fitur Tambahan

- Sistem ranking pengguna sebagai fitur tambahan, bukan bagian MVP.
- FAQ sebagai konten informasi, bukan tabel database pada desain saat ini.
- Berita sebagai konten yang dapat dikelola sistem.

## Scope

### In Scope

- Satu kecamatan sebagai wilayah sistem.
- Banyak desa di dalam kecamatan tersebut.
- Role: Super Admin Kecamatan, Admin Desa, Petugas Desa, dan Masyarakat.
- Mobile Flutter sebagai satu aplikasi untuk Masyarakat dan Petugas dengan UI berdasarkan role.
- Website Laravel dengan dashboard berbeda untuk Super Admin Kecamatan dan Admin Desa.
- Database PostgreSQL + PostGIS.
- Laporan sampah berbasis foto dan lokasi.
- Workflow validasi, penugasan, pembersihan, dan verifikasi.
- Heatmap/peta sampah aktif.
- Mark sampah selesai dengan masa tampil sementara.
- Penyimpanan media di luar database relasional; pilihan storage masih terbuka dengan Supabase Storage sebagai kandidat yang dipertimbangkan.
- Current weather tanpa histori cuaca.

### Out of Scope

- Pengelolaan seluruh Kabupaten Jember.
- Peta Kabupaten Jember sebagai map utama aplikasi.
- Operasional petugas lintas desa; petugas dimiliki oleh masing-masing desa.
- Membuka kembali laporan yang sudah `RESOLVED`.
- Penyimpanan histori cuaca pada MVP.
- Ranking pengguna sebagai bagian MVP.
- FAQ sebagai tabel database.
- Algoritma heatmap lanjutan seperti scoring kompleks, predictive analytics, atau AI.

## Success Criteria

1. Masyarakat dapat membuat laporan dengan foto dan lokasi, dan laporan tersebut masuk ke Admin Desa yang sesuai.
2. Admin Desa dapat memvalidasi laporan, menugaskan beberapa Petugas Desa, dan memverifikasi bukti pembersihan.
3. Petugas Desa dapat menerima/menolak pekerjaan, melakukan pembersihan, dan mengirim foto hasil.
4. Setelah verifikasi berhasil, laporan menjadi `RESOLVED`, bukti before/after dapat dilihat oleh masyarakat yang berhak, dan laporan tidak lagi dihitung sebagai sampah aktif pada heatmap.
5. Super Admin Kecamatan dapat memantau laporan dan kegiatan seluruh desa dalam satu kecamatan melalui dashboard.
