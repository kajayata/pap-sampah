# FITUR APLIKASI PAP SAMPAH

Dokumen ini berisi daftar fitur aplikasi berdasarkan role yang ada: **Super Admin (Kecamatan)**, **Admin Kelurahan**, **Masyarakat**, dan **Petugas Kebersihan**.

## 1. SUPER ADMIN (KECAMATAN) - WEB

*Hanya dapat akses via Web Dashboard Admin. Tidak dapat login ke aplikasi Mobile.*

### Sudah Ada (Implemented)
| Fitur | Status | Keterangan |
|---|---|---|
| **Login Web Admin** | ✅ Ada | Autentikasi khusus admin/super admin |
| **Dashboard Monitoring** | ✅ Ada | Statistik laporan per kelurahan, distribusi status laporan, petugas teraktif |
| **Monitoring Laporan Sampah** | ✅ Ada | Melihat seluruh laporan dari 7 kelurahan Sumbersari (bisa filter per kelurahan) |
| **Monitoring Kegiatan Pembersihan** | ✅ Ada | Tracking penugasan dan status pembersihan lintas kelurahan |
| **Peta & Heatmap Sebaran Sampah** | ✅ Ada | Visualisasi spasial dengan layer aktif/selesai, heatmap, bank sampah, TPA, batas kelurahan |
| **Filter Berdasarkan Desa/Kelurahan** | ✅ Ada | Filter laporan, monitoring per wilayah |
| **CRUD User (Masyarakat)** | ✅ Ada | Kelola akun masyarakat (list, edit, toggle status, hapus) |
| **CRUD Admin Kelurahan** | ✅ Ada | Kelola akun admin desa/kelurahan |
| **CRUD Petugas Kebersihan** | ✅ Ada | Dapat melihat/monitoring petugas lintas kelurahan |
| **CRUD Bank Sampah** | ✅ Ada | Kelola seluruh bank sampah di wilayah kecamatan |
| **CRUD TPA/TPS-3R** | ✅ Ada | Kelola fasilitas pembuangan akhir |
| **CRUD Berita & Edukasi** | ✅ Ada | Kelola berita dengan role-based permission (Super Admin bisa kelola semua berita) |
| **CRUD Kategori Sampah** | ✅ Ada | Kelola kategori sampah |
| **CRUD App Settings** | ✅ Ada | Konfigurasi aplikasi (model `AppSetting` tersedia) |
| **Rangkuman Kegiatan** | ✅ Ada | Statistik dan rekap kegiatan |
| **Notifikasi** | ⚠️ Partial | Model `Notification` tersedia, perlu integrasi UI lengkap |

### Belum Ada / Perlu Dikembangkan
| Fitur | Prioritas | Keterangan |
|---|---|---|
| **Laporan Statistik/Rekap Export** | Sedang | Export laporan per periode (PDF/Excel) |


---

## 2. ADMIN KELURAHAN - WEB

*Hanya dapat akses via Web Dashboard Admin. Hanya bisa mengelola data di kelurahannya sendiri.*

### Sudah Ada (Implemented)
| Fitur | Status | Keterangan |
|---|---|---|
| **Login Web Admin** | ✅ Ada | Autentikasi khusus admin kelurahan |
| **Dashboard Desa** | ✅ Ada | Statistik laporan & kegiatan khusus kelurahan sendiri |
| **Monitoring Laporan Sampah** | ✅ Ada | Hanya laporan di wilayah kelurahan admin (proteksi wilayah) |
| **Detail Laporan Sampah** | ✅ Ada | Melihat detail lengkap laporan + foto + lokasi |
| **Validasi Laporan Sampah** | ✅ Ada | Menyetujui laporan -> status `VALIDATED` |
| **Menolak Laporan + Alasan** | ✅ Ada | Menolak laporan dengan alasan wajib -> status `REJECTED` |
| **Membuat Cleanup Task (Penugasan)** | ✅ Ada | Menugaskan petugas kebersihan ke laporan yang tervalidasi |
| **Menentukan Petugas Kebersihan** | ✅ Ada | Penugasan multi-petugas ke 1 laporan |
| **Menentukan Lebih dari Satu Petugas** | ✅ Ada | Mendukung penugasan tim |
| **CRUD Petugas Kebersihan Desa** | ✅ Ada | Kelola petugas hanya di kelurahan sendiri |
| **Memberikan Deskripsi/Instruksi Pembersihan** | ✅ Ada | Input instruksi kerja & perlengkapan yang dibawa |
| **Monitoring Proses Pembersihan** | ✅ Ada | Tracking status tiap petugas (PENDING/ACCEPTED/REJECTED/COMPLETED) |
| **Verifikasi Hasil Pembersihan** | ✅ Ada | Persetujuan hasil pembersihan -> status `RESOLVED` |
| **Meminta Pembersihan Ulang** | ✅ Ada | Reject verifikasi -> kembali ke `IN_PROGRESS` |
| **Peta Laporan Sampah** | ✅ Ada | Visualisasi laporan di wilayah kelurahan |
| **Rangkuman Kegiatan** | ✅ Ada | Rekap kegiatan kelurahan |
| **Notifikasi** | ⚠️ Partial | Model tersedia, perlu integrasi UI |

### Belum Ada / Perlu Dikembangkan
| Fitur | Prioritas | Keterangan |
|---|---|---|
| **Notifikasi Real-time Web** | Tinggi | Notifikasi laporan masuk, penugasan, verifikasi |
| **Laporan Harian/Mingguan Kelurahan** | Sedang | Rekap kegiatan per periode |

---

## 3. MASYARAKAT - MOBILE APP

*Hanya dapat akses via Aplikasi Mobile Flutter. Tidak dapat login ke Web Dashboard Admin.*

### Sudah Ada (Implemented)
| Fitur | Status | Keterangan |
|---|---|---|
| **Registrasi** | ✅ Ada | Daftar akun masyarakat langsung di aplikasi |
| **Login** | ✅ Ada | Autentikasi berbasis Sanctum |
| **Profil** | ✅ Ada | Informasi profil pengguna |
| **Membuat Laporan Sampah** | ✅ Ada | Buat laporan dengan foto, GPS, kategori, deskripsi |
| **Upload Foto Bukti** | ✅ Ada | Upload foto kondisi/lokasi sampah (multiple foto) |
| **Mengambil Lokasi GPS** | ✅ Ada | Ambil koordinat lokasi saat pelaporan |
| **Memilih Kategori Sampah** | ✅ Ada | Pilih kategori dari master data |
| **Menambahkan Deskripsi Laporan** | ✅ Ada | Deskripsi kondisi sampah |
| **Validasi Lokasi Desa** | ✅ Ada | Validasi spasial PostGIS - hanya dalam wilayah Sumbersari (7 kelurahan) |
| **Melihat Riwayat Laporan** | ✅ Ada | Daftar seluruh laporan yang pernah dibuat |
| **Melihat Detail Laporan** | ✅ Ada | Detail lengkap, timeline status, foto, lokasi |
| **Melihat Status Laporan** | ✅ Ada | Status real-time (SUBMITTED/VALIDATED/ASSIGNED/IN_PROGRESS/PENDING_VERIFICATION/RESOLVED/REJECTED) |
| **Melihat Peta Sampah** | ✅ Ada | Peta spasial dengan laporan aktif/selesai |
| **Melihat Bank Sampah** | ✅ Ada | Direktori bank sampah di wilayah |
| **Melihat TPA/TPS-3R** | ✅ Ada | Direktori fasilitas pembuangan |
| **Melihat Cuaca** | ✅ Ada | Prakiraan cuaca Kecamatan Sumbersari (Open-Meteo) |
| **Notifikasi** | ⚠️ Partial | Model & provider tersedia, perlu integrasi push notification penuh |

### Belum Ada / Perlu Dikembangkan
| Fitur | Prioritas | Keterangan |
|---|---|---|
| **Edit/Hapus Laporan** | Tinggi | Edit/hapus laporan dengan kondisi tertentu (hanya status SUBMITTED?) |
| **Ubah Profil/Password** | Tinggi | Pengaturan akun (edit profil, ganti password) |
| **Reset Password** | Tinggi | Lupa password / reset password via email/OTP |


---

## 4. PETUGAS KEBERSIHAN - MOBILE APP

*Hanya dapat akses via Aplikasi Mobile Flutter. Akun dibuat oleh Admin Kelurahan.*

### Sudah Ada (Implemented)
| Fitur | Status | Keterangan |
|---|---|---|
| **Login** | ✅ Ada | Autentikasi berbasis Sanctum |
| **Profil** | ✅ Ada | Informasi profil petugas |
| **Daftar Tugas Pembersihan** | ✅ Ada | List tugas yang ditugaskan ke petugas |
| **Detail Tugas** | ✅ Ada | Detail lengkap laporan, lokasi, instruksi, tim |
| **Melihat Lokasi Sampah** | ✅ Ada | Navigasi ke lokasi laporan |
| **Melihat Foto Laporan** | ✅ Ada | Foto kondisi awal dari warga |
| **Melihat Deskripsi/Instruksi Pembersihan** | ✅ Ada | Instruksi kerja & perlengkapan dari admin |
| **Menerima Tugas** | ✅ Ada | Accept tugas pembersihan |
| **Menolak Tugas + Alasan** | ✅ Ada | Reject tugas dengan alasan wajib |
| **Memulai Pembersihan** | ✅ Ada | Status berubah saat terima tugas |
| **Upload Foto Progres (BEFORE)** | ✅ Ada | Upload foto saat mulai/bukti |
| **Upload Foto Setelah Pembersihan (AFTER)** | ✅ Ada | Upload foto hasil pembersihan (minimal 1 foto AFTER untuk menyelesaikan) |
| **Upload Lokasi Pembersihan** | ✅ Ada | Tracking lokasi saat pengerjaan |
| **Menyelesaikan Tugas** | ✅ Ada | Tandai tugas selesai (menunggu verifikasi admin) |
| **Riwayat Tugas** | ✅ Ada | Daftar tugas yang telah dikerjakan |
| **Notifikasi** | ⚠️ Partial | Model & provider tersedia |

### Belum Ada / Perlu Dikembangkan
| Fitur | Prioritas | Keterangan |
|---|---|---|
| **Ubah Profil/Password** | Tinggi | Pengaturan akun petugas |
| **Reset Password** | Tinggi | Reset password petugas |
| **Laporan Hasil Kerja Harian** | Sedang | Rekap tugas per hari |
| **Filter Riwayat Tugas** | Sedang | Filter berdasarkan status/tanggal di riwayat |

---

## FITUR UMUM/PLATFORM

### Sudah Ada
| Fitur | Platform | Status | Keterangan |
|---|---|---|---|
| **Landing Page Publik** | Web | ✅ Ada | Halaman utama tanpa login (`/`, `/home`) |
| **Akses Publik Tanpa Login** | Web | ✅ Ada | Landing page, peta publik, berita, bank sampah, TPA dapat diakses umum |
| **REST API Publik** | API | ✅ Ada | categories, map, waste-banks, landfills, news, weather, settings |
| **REST API Terproteksi (Sanctum)** | API | ✅ Ada | reports, tasks, me, logout untuk mobile |
| **Spasial PostGIS** | Backend | ✅ Ada | Validasi wilayah, heatmap, boundary polygon 7 kelurahan |
| **Upload & Kompresi Foto** | Backend | ✅ Ada | Auto-orient, resize 1280px, kompresi JPEG 70% |
| **Multi-role Authorization** | Backend | ✅ Ada | Role-based access dengan proteksi wilayah per kelurahan |

### Belum Ada / Perlu Dikembangkan
| Fitur | Prioritas | Keterangan |
|---|---|---|
| **Push Notification (FCM)** | Tinggi | Notifikasi real-time untuk mobile (warga & petugas) |
| **Notifikasi Web/Admin** | Tinggi | Notifikasi di dashboard web untuk admin |
| **Audit Trail UI** | Sedang | Tampilan history perubahan status laporan |

---

## CATATAN PENTING

- **Role terpisah**: Super Admin (Kecamatan), Admin Kelurahan, Masyarakat, Petugas Kebersihan memiliki akses terpisah
- **Pembatasan wilayah**: Admin Kelurahan **hanya bisa mengelola data di kelurahannya sendiri** (petugas, laporan, tugas)
- **Akses platform**: 
  - Web Dashboard → Hanya untuk Super Admin & Admin Kelurahan
  - Mobile App → Hanya untuk Masyarakat & Petugas Kebersihan
  - Halaman publik web → Dapat diakses tanpa login
- **Validasi spasial**: Laporan warga hanya bisa dibuat di dalam wilayah Kecamatan Sumbersari (7 kelurahan)
- **Alur kerja**: Masyarakat lapor (mobile) → Admin Kelurahan validasi & tugaskan → Petugas terima & kerjakan (mobile) → Admin verifikasi → Selesai
