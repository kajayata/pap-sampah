# Dokumentasi Bug & Kebutuhan
## Halaman Admin (Kelurahan)

Dokumentasi ini berisi bug/error dan kebutuhan yang ditemukan pada halaman Admin Kelurahan.

---

## A. BUG / ERROR

### 1. Tampilan Left Margin Tidak Sesuai

**Halaman:** Laporan Sampah

**Masalah:**  
Jarak bagian kiri halaman tidak sesuai dengan tampilan yang seharusnya.

**Harapan:**  
Jarak kiri halaman dibuat rapi dan konsisten dengan halaman lainnya.

**Jenis:** UI

---

### 2. Jam Validasi Laporan Tidak Sesuai

**Halaman:** Laporan Sampah

**Masalah:**  
Jam yang tercatat saat laporan divalidasi tidak sesuai dengan waktu sebenarnya.

**Harapan:**  
Jam validasi harus sesuai dengan waktu ketika admin melakukan validasi.

**Jenis:** Logic System

---

### 3. Jam Penugasan Petugas Tidak Sesuai

**Halaman:** Laporan Sampah

**Masalah:**  
Jam yang tercatat saat laporan diberikan kepada petugas tidak sesuai dengan waktu sebenarnya.

**Harapan:**  
Jam penugasan harus sesuai dengan waktu ketika admin memberikan tugas kepada petugas.

**Jenis:** Logic System

---

### 4. Email Tidak Sesuai Masih Bisa Digunakan

**Halaman:** Pembuatan Akun Petugas

**Masalah:**  
Email dengan format yang tidak sesuai masih bisa digunakan untuk membuat akun petugas.

**Harapan:**  
Sistem harus menolak email yang formatnya tidak sesuai.

**Contoh format jika memang ditentukan:**

```text
petugas[no][kelurahan]@papsampah.id
```

**Jenis:** Validation / Logic System

---

### 5. Nomor Telepon Bisa Digunakan Lebih dari Satu Akun

**Halaman:** Pembuatan Akun Petugas

**Masalah:**  
Nomor telepon yang sudah digunakan masih bisa digunakan untuk membuat akun petugas lain.

**Harapan:**  
Satu nomor telepon hanya boleh digunakan untuk satu akun petugas.

**Jenis:** Validation / Logic System

---

# B. KEBUTUHAN / NEED

### 1. Tombol Tampilkan Password

Pada saat membuat akun petugas, tambahkan tombol **Show/Hide Password** agar admin dapat melihat password yang sedang dibuat.

---

### 2. Validasi Email

Jika sistem memiliki format email khusus, sistem harus mengecek format email sebelum akun dibuat.

Jika format salah, tampilkan pesan seperti:

> Format email tidak sesuai.

---

### 3. Validasi Nama Petugas

Jika sistem memiliki aturan khusus untuk nama petugas, sistem harus mengecek nama sebelum akun dibuat.

Contoh:

```text
Nama Lengkap Petugas (Kelurahan)
```

---

### 4. Validasi Nomor Telepon

Sebelum akun dibuat, sistem harus mengecek apakah nomor telepon sudah digunakan.

Jika sudah digunakan, tampilkan pesan seperti:

> Nomor telepon sudah terdaftar.

Jika belum digunakan, akun dapat dibuat.