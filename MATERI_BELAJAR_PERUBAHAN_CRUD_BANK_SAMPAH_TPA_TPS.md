# Materi Belajar: CRUD Bank Sampah dan TPA/TPS-3R

> Materi ini menjelaskan perubahan Bank Sampah dan TPA/TPS-3R yang dibuat pada project Laravel ini. Tujuannya agar alur kode mudah diikuti saat perlu mengubah aturan atau menambah perilaku baru.

## 1. Peta Alur Program

Saat pengguna mengirim form atau menekan tombol di halaman admin, alurnya seperti ini:

```text
Browser / Blade
    -> Route di routes/web.php
    -> Controller memvalidasi input dan menjalankan aturan
    -> Model atau query database membaca/mengubah data
    -> Redirect kembali ke halaman
    -> Blade menampilkan data atau pesan
```

Test fitur mengirim request seperti browser, lalu memeriksa hasilnya. Jadi, saat mencari tempat untuk mengubah sesuatu, mulai dari perilakunya:

| Yang ingin diubah | Tempat utama |
| --- | --- |
| Alamat URL atau HTTP method | `web/laravel/routes/web.php` |
| Validasi, pencarian, duplikat, simpan, edit, hapus | Controller di `web/laravel/app/Http/Controllers/Web/` |
| Tampilan tabel, tombol, form, placeholder | Blade di `web/laravel/resources/views/` |
| Tipe atau batas kolom database | Migration di `web/laravel/database/migrations/` |
| Bukti bahwa perilaku bekerja | `web/laravel/tests/Feature/Fase4FeatureTest.php` |

Bank Sampah menggunakan tabel `waste_banks` dan model `WasteBank`. Data TPA dan TPS-3R disimpan bersama dalam tabel `landfills` melalui model `Landfill`.

## 2. Membatasi Nomor Telepon

Nomor telepon Bank Sampah dibatasi menjadi maksimal 13 digit di tiga lapisan.

### Form browser

File: `web/laravel/resources/views/waste_banks/form.blade.php`

```html
maxlength="13"
inputmode="numeric"
pattern="[0-9]{1,13}"
```

- `maxlength` membatasi jumlah karakter yang dapat diketik.
- `inputmode="numeric"` meminta keyboard angka pada perangkat yang mendukungnya.
- `pattern` meminta nilai berisi 1 sampai 13 digit.

Ini membantu pengguna, tetapi tetap bisa dilewati oleh request buatan sendiri. Karena itu, aturan penting harus dicek ulang di server.

### Validasi server

File: `web/laravel/app/Http/Controllers/Web/WasteBankController.php`, pada method `store()` dan `update()`.

```php
'phone' => ['nullable', 'string', 'regex:/^[0-9]{1,13}$/'],
```

- `nullable`: nomor boleh tidak diisi.
- `string`: nilainya diterima sebagai teks agar angka nol di depan tidak hilang.
- Regex `^[0-9]{1,13}$`: seluruh nilai hanya boleh berisi digit, dengan panjang 1-13.

### Batas kolom database

Migration: `web/laravel/database/migrations/2026_09_30_000001_limit_waste_bank_phone_to_13_characters.php`

```php
$table->string('phone', 13)->nullable()->change();
```

Migration membuat batas 13 karakter tetap berlaku di database. Bagian `down()` mengembalikan batas kolom menjadi 30 jika migration di-rollback.

**Jika batas telepon berubah lagi**, sesuaikan ketiga tempat tersebut: atribut form, validasi di `store()` dan `update()`, serta migration baru. Migration yang sudah dijalankan sebaiknya tidak diedit untuk mengubah database yang sudah terpasang; buat migration baru.

## 3. Mencegah Data Duplikat

Pengecekan berada di `store()` dan `update()` kedua controller:

- `WasteBankController.php` untuk tabel Bank Sampah.
- `LandfillController.php` untuk tabel TPA/TPS-3R.

Aturan yang digunakan adalah kelurahan, nama, dan alamat harus sama agar dianggap duplikat. Nama dan alamat dibandingkan tanpa membedakan huruf besar/kecil, serta spasi di awal/akhir diabaikan.

Contoh bentuk query:

```php
$duplicate = WasteBank::where('village_id', $villageId)
    ->whereRaw('LOWER(TRIM(name)) = LOWER(TRIM(?))', [$name])
    ->whereRaw('LOWER(TRIM(address)) = LOWER(TRIM(?))', [$address])
    ->exists();
```

- `where('village_id', ...)` membatasi pemeriksaan ke kelurahan yang sama.
- `LOWER(TRIM(...))` membuat pencocokan nama/alamat tidak sensitif terhadap kapitalisasi dan spasi tepi.
- `?` dan array parameter membuat nilai pengguna dikirim sebagai parameter query, bukan disambung langsung ke SQL.
- `exists()` hanya meminta jawaban ada atau tidak, tanpa mengambil seluruh record.

Pada `update()`, ada kondisi tambahan:

```php
->where('id', '!=', $id)
```

Kondisi itu mengabaikan record yang sedang diedit. Tanpanya, record akan dianggap duplikat dari dirinya sendiri.

Jika ditemukan duplikat, controller melakukan:

```php
return back()->withInput()->with('error', 'Data sudah ada sebelumnya');
```

`back()` kembali ke halaman sebelumnya, `withInput()` mengisi ulang nilai form, dan `with('error', ...)` menyimpan pesan sementara di session.

**Batas implementasi saat ini:** pengecekan dilakukan oleh aplikasi sebelum insert/update, bukan oleh constraint unik database. Jika aplikasi nantinya menerima banyak penulisan bersamaan, pertimbangkan constraint database yang sesuai agar dua request serentak tidak bisa menyimpan duplikat.

Untuk mengubah definisi duplikat, ubah query tersebut di method `store()` dan `update()` pada kedua controller, lalu sesuaikan test duplikat.

## 4. Menampilkan Pesan Error

File: `web/laravel/resources/views/layouts/app.blade.php`

Layout membaca pesan dari session:

```blade
@if(session('error'))
    <div role="alert">
        {{ session('error') }}
    </div>
@endif
```

Controller mengirim pesannya dengan `with('error', ...)`. Layout adalah tempat bersama, jadi alert ini dapat digunakan oleh halaman admin lain yang mengirim session dengan key `error`.

Jika pesan tidak tampil, periksa dua hal: apakah controller memakai key session yang sama (`error`), dan apakah halaman menggunakan layout `layouts.app`.

## 5. Mencari Data Bank Sampah

Pencarian berada pada method `index()` di `WasteBankController.php`. Form GET ada di `resources/views/waste_banks/index.blade.php`.

Keyword dicocokkan ke:

- nama;
- alamat;
- telepon;
- deskripsi;
- nama kelurahan melalui relasi `village`.

Contoh:

```php
$q->where('name', 'ilike', $search)
  ->orWhere('address', 'ilike', $search)
  ->orWhere('phone', 'ilike', $search)
  ->orWhere('description', 'ilike', $search)
  ->orWhereHas('village', function ($villageQuery) use ($search) {
      $villageQuery->where('name', 'ilike', $search);
  });
```

`orWhere` berarti record cocok jika salah satu kolom cocok. `orWhereHas` mencari melalui relasi model ke kelurahan. `ilike` adalah pencocokan teks tanpa membedakan kapitalisasi pada PostgreSQL.

Keyword tepat `aktif` atau `nonaktif` juga menambahkan pencarian berdasarkan kolom boolean `is_active`. Jika ingin menambah kolom pencarian, tambahkan kondisi di dalam grup `where(function ($q) { ... })` yang sama agar kondisi `OR` tidak melewati filter kelurahan.

## 6. Menambahkan Aksi Hapus

Penghapusan membutuhkan tiga bagian.

### Route

`web/laravel/routes/web.php` menghubungkan HTTP `DELETE` ke method controller:

```php
Route::delete('/bank-sampah/{id}', [WasteBankController::class, 'destroy'])
    ->name('waste-banks.destroy');
```

Route TPA/TPS-3R mengikuti pola yang sama dengan `LandfillController` dan nama route `landfills.destroy`. Route ini berada di dalam grup middleware `auth`.

### Tombol Blade

Form HTML biasa tidak mendukung method `DELETE`, jadi Laravel menggunakan method spoofing:

```blade
<form method="POST" action="{{ route('waste-banks.destroy', $wb->id) }}"
      onsubmit="return confirm('Yakin ingin menghapus data ini?')">
    @csrf
    @method('DELETE')
    <button type="submit">Hapus</button>
</form>
```

`@csrf` melindungi form dari request lintas situs. `@method('DELETE')` memberi tahu Laravel bahwa request POST ini dimaksudkan sebagai DELETE. `confirm()` memberi kesempatan membatalkan sebelum pengiriman.

### Controller

Method `destroy()` mencari record dengan `findOrFail()`, menghapusnya, lalu redirect dengan pesan sukses. Pada Bank Sampah, method juga mengecek role dan kelurahan pengguna sebelum menghapus. Penghapusan Landfill saat ini hanya dibatasi oleh middleware login; jika aturan peran/kelurahan dibutuhkan, tambahkan otorisasi di `LandfillController::destroy()`.

## 7. Membaca dan Menambah Test

Test fitur ada di `web/laravel/tests/Feature/Fase4FeatureTest.php`. Test menguji perilaku melalui HTTP, bukan hanya memanggil method controller langsung.

Contoh pola pemeriksaan validasi:

```php
$this->actingAs($user)
    ->post('/bank-sampah', $input)
    ->assertRedirect('/bank-sampah/create')
    ->assertSessionHasErrors('phone');
```

Contoh pola penghapusan:

```php
$this->actingAs($user)
    ->delete(route('waste-banks.destroy', $id))
    ->assertRedirect(route('waste-banks.index'));

$this->assertDatabaseMissing('waste_banks', ['id' => $id]);
```

Jalankan test dari folder `web/laravel`:

```powershell
php artisan test --filter=Fase4FeatureTest
```

Saat mengubah aturan, tambahkan atau perbarui test yang menunjukkan kondisi berhasil dan kondisi yang harus ditolak. Contoh: nomor 13 digit diterima, 14 digit ditolak; record berbeda boleh disimpan, sedangkan record yang memenuhi aturan duplikat harus ditolak.

## 8. Checklist Sebelum Mengubah Kode

1. Tentukan perilaku yang diinginkan dan data mana yang menjadi dasar aturan.
2. Temukan route, method controller, Blade, dan migration yang terkait.
3. Ubah validasi server terlebih dahulu untuk aturan input; HTML hanya membantu pengguna.
4. Jika struktur database berubah, buat migration baru dengan metode `up()` dan `down()`.
5. Tambahkan atau perbarui feature test untuk hasil yang diharapkan.
6. Jalankan test terkait dan periksa `git diff --check`.

Jangan mengubah modul API atau tabel lain jika permintaan hanya menyangkut CRUD admin Bank Sampah dan TPA/TPS-3R.