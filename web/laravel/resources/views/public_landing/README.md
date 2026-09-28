# Landing Page `/home`

Dokumentasi ini hanya berlaku untuk landing page publik SampahJember pada route `/home`. Tampilan ini terpisah dari layout dashboard admin dan tidak mengubah route `/` atau `/login`.

## Route

```php
GET /home
```

Route tersebut menggunakan nama `public.home` dan diarahkan ke:

```text
app/Http/Controllers/Web/SampahJemberLandingController.php
```

## Struktur File

```text
web/laravel/
├── app/Http/Controllers/Web/
│   └── SampahJemberLandingController.php
├── resources/
│   ├── css/
│   │   └── sampah-jember-landing.css
│   ├── js/
│   │   └── sampah-jember-landing.js
│   └── views/
│       ├── layouts/
│       │   └── public_landing.blade.php
│       └── public_landing/
│           ├── index.blade.php
│           ├── README.md
│           └── partials/
│               ├── about.blade.php
│               ├── download.blade.php
│               ├── footer.blade.php
│               ├── heatmap.blade.php
│               ├── hero.blade.php
│               ├── kecamatan.blade.php
│               ├── navbar.blade.php
│               └── panduan.blade.php
└── public/
    └── sampah-jember/icons/
        ├── apple1.svg
        ├── badge-check.svg
        ├── chevron-green.svg
        ├── download.svg
        ├── google-play.svg
        └── play.svg
```

## Alur Render

```text
GET /home
    -> SampahJemberLandingController@index
    -> query Kecamatan Sumbersari dan 7 kelurahan aktif
    -> query statistik laporan dari PostgreSQL
    -> ambil data peta melalui MapService
    -> public_landing.index
    -> layouts.public_landing
    -> partials landing
```

## Tanggung Jawab File

### Controller

`SampahJemberLandingController.php` menyiapkan data untuk halaman publik:

- Kecamatan aktif berdasarkan kode wilayah `35.09.20`.
- Daftar kelurahan aktif dalam kecamatan tersebut.
- Jumlah total, aktif, dan selesai laporan.
- Data heatmap laporan aktif.
- Marker laporan aktif dan laporan selesai dalam periode H+7.
- Boundary kelurahan.
- Bank Sampah dan TPA/TPS-3R aktif.
- Enam langkah penggunaan aplikasi mobile.

Controller tidak menangani login atau mutation data. Landing page bersifat publik.

### Layout

`resources/views/layouts/public_landing.blade.php` adalah layout khusus landing page.

Layout ini memuat:

- Font Fraunces dan Outfit.
- Tailwind CSS CDN.
- Entry Vite `sampah-jember-landing.css` dan `sampah-jember-landing.js`.
- Navbar publik.
- Isi halaman.
- Footer publik.

Jangan mengganti file ini dengan `resources/views/layouts/app.blade.php` karena `app.blade.php` digunakan dashboard admin.

### Halaman Utama

`resources/views/public_landing/index.blade.php` menyusun section landing secara berurutan:

1. Hero.
2. Informasi platform.
3. Peta dan heatmap.
4. Data kelurahan.
5. Panduan penggunaan aplikasi.
6. Download aplikasi.

Ranking tidak ditampilkan karena merupakan fitur tambahan di luar scope MVP.

### Partial

- `navbar.blade.php`: navigasi anchor publik dan menu mobile.
- `hero.blade.php`: judul, status wilayah, CTA, dan statistik utama.
- `about.blade.php`: penjelasan platform dan ringkasan statistik.
- `heatmap.blade.php`: peta Leaflet, marker laporan, heatmap, boundary, Bank Sampah, TPA, layer toggle, dan modal foto before/after.
- `kecamatan.blade.php`: pencarian dan ringkasan laporan per kelurahan.
- `panduan.blade.php`: enam langkah alur masyarakat menggunakan aplikasi mobile.
- `download.blade.php`: CTA dan mockup aplikasi mobile.
- `footer.blade.php`: informasi publik dan navigasi footer.

## Data dan Workflow

Landing page mengikuti scope project pada `docs/context`:

- Scope hanya Kecamatan Sumbersari.
- Laporan selesai tidak dihitung sebagai titik aktif heatmap.
- Marker selesai dapat ditampilkan sementara sesuai `marker_display_days`.
- Validasi, penugasan, pembersihan, dan verifikasi tetap dilakukan melalui workflow aplikasi.
- Website publik hanya menampilkan informasi; pelaporan dilakukan melalui aplikasi mobile.
- Authorization dan business rule tetap berada di backend, bukan di JavaScript landing.

## Asset dan Frontend

Asset landing menggunakan nama khusus agar tidak bertabrakan dengan dashboard:

- CSS: `resources/css/sampah-jember-landing.css`
- JavaScript: `resources/js/sampah-jember-landing.js`
- Icon: `public/sampah-jember/icons/`

Jika entry CSS atau JS diubah, jalankan:

```powershell
cd web/laravel
npm run build
```

Jangan mengedit file hasil generate di `public/build` secara manual.

## Menjalankan dan Mengecek

Dari folder `web/laravel`:

```powershell
php artisan serve --host=127.0.0.1 --port=8000
```

Buka:

```text
http://127.0.0.1:8000/home
```

Validasi dasar:

```powershell
php artisan view:cache
php artisan route:list --path=home
php artisan test
npm run build
```

Route dashboard dan auth yang harus tetap terpisah:

```text
/       -> redirect login atau dashboard
/login  -> auth website admin
/home   -> landing publik SampahJember
```
