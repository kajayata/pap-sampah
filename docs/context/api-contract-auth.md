# API Contract — Auth (Register, Login, Logout, Me)

> Dokumen ini untuk tim mobile Flutter. Berisi kontrak request/response,
> bukan implementasi UI. Base URL sesuaikan dengan environment
> (`http://localhost:8000/api` untuk dev lokal).

## POST /api/register

Registrasi akun baru. **Selalu jadi role `masyarakat`** — tidak ada cara
membuat role lain lewat endpoint ini.

**Request body (JSON):**
```json
{
  "name": "Rojali",
  "email": "rojali@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "081234567890"
}
```
`phone` opsional. `password` minimal 8 karakter.

**Response 201 (sukses):**
```json
{
  "user": { "id": 1, "name": "Rojali", "email": "rojali@example.com", "phone": "081234567890", "is_active": true },
  "token": "1|abcdef123456..."
}
```

**Response 422 (validasi gagal):**
```json
{
  "message": "The email has already been taken.",
  "errors": { "email": ["Email sudah terdaftar."] }
}
```

## POST /api/login

**Request body:**
```json
{ "email": "rojali@example.com", "password": "password123" }
```

**Response 200:**
```json
{
  "user": { "id": 1, "name": "Rojali", "email": "...", "role": { "id": 4, "name": "masyarakat" } },
  "token": "2|xyz789..."
}
```

**Response 422:** email/password salah, atau akun tidak aktif — pesan error selalu digeneralisasi di field `email` (tidak membocorkan mana yang salah).

## Autentikasi untuk endpoint terproteksi

Simpan `token` dari register/login (disarankan `flutter_secure_storage`, bukan `SharedPreferences` biasa, karena ini credential). Kirim di setiap request berikutnya:

```
Authorization: Bearer {token}
```

## POST /api/logout *(butuh token)*
**Response 200:** `{ "message": "Logout berhasil." }`

## GET /api/me *(butuh token)*
Mengembalikan data user yang sedang login, termasuk `role` dan `village`.

## Catatan penting
- Semua request **wajib** lewat endpoint ini — jangan pernah mengakses Supabase langsung dari Flutter (keputusan arsitektur Step 1).
- Token tidak expire otomatis di sisi server (Sanctum default) — kalau butuh expiry policy, ini open question terpisah, belum diputuskan.
