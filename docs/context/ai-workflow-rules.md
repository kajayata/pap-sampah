# AI Workflow Rules

## Approach

Bangun Pap Sampah secara incremental dan spec-driven. Context files menjadi sumber utama untuk product definition, architecture, code standards, dan progress. AI harus mengimplementasikan berdasarkan keputusan yang sudah ada dan tidak boleh menciptakan behavior produk yang belum didefinisikan.

## Scoping Rules

- Kerjakan satu feature unit pada satu waktu.
- Utamakan perubahan kecil yang dapat diverifikasi.
- Jangan menggabungkan perubahan pada mobile, website, database, dan integration yang tidak berkaitan dalam satu langkah besar.
- Untuk perubahan database, tetapkan schema/relationship terlebih dahulu sebelum membuat consumer pada mobile atau website.
- Jangan memperluas scope di luar satu kecamatan tanpa keputusan produk yang baru.

## When to Split Work

Pisahkan pekerjaan jika satu langkah mencampur:

- perubahan UI mobile dengan perubahan business workflow yang belum dipastikan;
- website/dashboard dengan migration database yang tidak terkait;
- integrasi weather dengan workflow laporan sampah;
- perubahan storage media dengan feature bisnis lain;
- lebih dari satu feature yang tidak dapat diverifikasi sebagai satu alur end-to-end yang jelas.

Jika perubahan tidak dapat diuji secara terukur dalam satu unit kecil, pecah menjadi beberapa unit.

## Handling Missing Requirements

- Jangan mengarang behavior yang belum ada pada context.
- Jika requirement ambigu, berhenti pada keputusan yang ambigu dan tanyakan/resolve sebelum implementasi.
- Jika requirement belum diputuskan, masukkan ke `progress-tracker.md` sebagai open question.
- Jangan mengubah open question menjadi keputusan final hanya karena ada pilihan teknis yang terlihat masuk akal.
- Perhatikan perbedaan antara product decision dan technical implementation detail.

## Protected Files

- `context/ui-context.md` tidak boleh diubah oleh workflow backend/system-design ini karena file tersebut dikelola oleh anggota tim lain, kecuali ada instruksi eksplisit dari pemilik project.
- Jangan memodifikasi library pihak ketiga atau generated files tanpa alasan dan instruksi yang jelas.

## Keeping Docs in Sync

Setiap perubahan yang memengaruhi:

- scope fitur,
- system boundary,
- storage model,
- authentication/authorization,
- database/data model,
- code convention,

harus disinkronkan ke context file yang relevan sebelum pekerjaan berikutnya dilanjutkan.

`progress-tracker.md` wajib diperbarui setelah setiap perubahan implementasi yang bermakna.

## Before Moving to the Next Unit

1. Feature unit selesai sesuai scope yang telah didefinisikan.
2. Invariant pada `architecture.md` tidak dilanggar.
3. `progress-tracker.md` menggambarkan kondisi terbaru.
4. Migration/schema dapat diterapkan tanpa konflik pada environment target.
5. Test dan/atau static analysis yang relevan untuk unit tersebut berhasil.
6. Tidak ada requirement baru yang diam-diam ditambahkan di luar context.
