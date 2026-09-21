# UI Context — SampahJember

## Theme

Light mode only. Design language adalah platform publik pemerintah yang bersih dan terpercaya — latar terang berbasis krem, permukaan putih berlapis, dan palet warna brand hijau–kuning–oranye–merah sebagai aksen hierarki data. Tipografi memadukan serif display ekspresif (Fraunces) dengan sans-serif utilitas modern (Outfit).

---

## Colors

Semua komponen **wajib** menggunakan CSS custom property berikut — tidak ada hardcoded hex.

| Role              | CSS Variable          | Value     |
| ----------------- | --------------------- | --------- |
| Page background   | `--bg-base`           | `#FAFAF5` |
| Surface / card    | `--bg-surface`        | `#FFFFFF` |
| Emphasis surface  | `--bg-emphasis`       | `#5B7E3C` |
| Primary text      | `--text-primary`      | `#1C1C1C` |
| Muted text        | `--text-muted`        | `rgba(28,28,28,0.60)` |
| Inverse text      | `--text-inverse`      | `#FFFFFF` |
| Accent – primary  | `--accent-primary`    | `#5B7E3C` |
| Accent – secondary| `--accent-secondary`  | `#FFD65A` |
| Accent – warning  | `--accent-warning`    | `#FF9D23` |
| Accent – danger   | `--accent-danger`     | `#EA5252` |
| Border default    | `--border-default`    | `rgba(0,0,0,0.08)` |
| Border accent     | `--border-accent`     | `rgba(91,126,60,0.20)` |
| State – success   | `--state-success`     | `#5B7E3C` |
| State – warning   | `--state-warning`     | `#FF9D23` |
| State – error     | `--state-error`       | `#EA5252` |

### Intensitas Sampah (Heatmap)
| Level   | Warna     | Hex       |
| ------- | --------- | --------- |
| Tinggi  | Merah     | `#EA5252` |
| Sedang  | Oranye    | `#FF9D23` |
| Rendah  | Kuning    | `#FFD65A` |

---

## Typography

| Role          | Font                   | CSS Variable      | Weight        |
| ------------- | ---------------------- | ----------------- | ------------- |
| Display / judul | Fraunces (serif)     | `--font-display`  | 400 600 700 900 |
| Body / UI     | Outfit (sans-serif)    | `--font-sans`     | 300 400 500 600 700 |

```css
/* Google Fonts import — letakkan sebelum @import lainnya */
@import url('https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700;9..144,900&family=Outfit:wght@300;400;500;600;700&display=swap');
```

- **Heading utama (H1):** Fraunces, `text-5xl md:text-7xl`, weight `font-black` (900)
- **Section heading (H2):** Fraunces, `text-4xl md:text-5xl`, weight `font-bold` (700)
- **Card heading (H3):** Fraunces, `text-xl–2xl`, weight `font-bold` (700)
- **Body paragraph:** Outfit, `text-base`, `leading-relaxed`, warna `--text-muted`
- **Label / caption:** Outfit, `text-xs–sm`, weight `font-medium`
- **Angka statistik:** Fraunces, `font-black`, warna aksen brand

---

## Border Radius

| Konteks                  | Tailwind Class   | Nilai   |
| ------------------------ | ---------------- | ------- |
| Badge / pill / inline    | `rounded-full`   | 9999px  |
| Tombol                   | `rounded-xl`     | 12px    |
| Kartu / panel kecil      | `rounded-2xl`    | 16px    |
| Panel besar / section    | `rounded-3xl`    | 24px    |
| Avatar kotak             | `rounded-xl`     | 12px    |
| Input field              | `rounded-2xl`    | 16px    |

---

## Spacing System

Menggunakan skala Tailwind default (base 4px). Panduan utama:

- **Section vertical padding:** `py-24` (96px atas-bawah)
- **Container max-width:** `max-w-7xl mx-auto px-6`
- **Gap antar kartu:** `gap-4` hingga `gap-6`
- **Gap antar section heading dan konten:** `mb-14`
- **Inner card padding:** `p-5` hingga `p-6`

---

## Component Library

Tailwind CSS v4 murni — tidak ada komponen library eksternal. Semua komponen dibangun dari utiliti Tailwind. Komponen reusable ditulis sebagai React function component dalam `src/App.tsx`.

---

## Komponen Utama

### NavBar
- `fixed top-0`, transparan di hero, `bg-white/95 backdrop-blur-md` saat discroll
- Logo: kotak hijau `#5B7E3C` + teks Fraunces, aksen teks `#FFD65A`
- Nav link aktif: `bg-[#5B7E3C] text-white rounded-md`
- CTA "Unduh App": `bg-[#FFD65A] text-[#1C1C1C]` selalu
- Mobile: hamburger menu dengan dropdown `bg-white`

### Hero Section
- Background: `linear-gradient(160deg, #1a2e0d → #2d4d1a → #5B7E3C → #3d5828)`
- Teks heading: putih + `#FFD65A` untuk kata kunci
- Stat badge (kanan): `bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl`
- Tombol primer: `bg-[#5B7E3C]`, tombol sekunder: `bg-[#FFD65A] text-[#1C1C1C]`
- Bottom curve SVG warna `#FAFAF5`

### Heatmap Grid
- Container: `bg-[#FAFAF5] rounded-3xl`, grid 5 kolom
- Setiap sel: `h-14 rounded-xl`, warna fill sesuai intensitas
- Hover: `scale(1.05)`, opacity naik ke 1, box-shadow warna aksen
- Panel kanan: kartu detail hover + kartu prioritas

### Kartu Kecamatan
- `bg-white rounded-2xl p-5 border-2`
- Border default: `border-transparent`, aktif/hover: `border-[#5B7E3C]`
- Progress bar: `h-1.5 bg-[#5B7E3C]/8 rounded-full` → fill `bg-[#5B7E3C]`

### Ranking Podium
- Tab switcher: `bg-[#FAFAF5] rounded-2xl p-1`, aktif: `bg-[#5B7E3C] text-white`
- Podium #1 di tengah, di-offset `-mt-6`
- Medal: Emas `#FFD65A`, Perak `#adb5bd`, Perunggu `#FF9D23`
- List item: `bg-[#FAFAF5] hover:bg-[#5B7E3C]/5 rounded-xl`

### Langkah Panduan (Step Card)
- `bg-white rounded-2xl p-6 border border-black/5`
- Hover: `shadow-md -translate-y-0.5`
- Nomor langkah: Fraunces `text-5xl font-black text-[#5B7E3C]/12` (ghosted)

### Download Section
- Background: `linear-gradient(135deg, #2d4d1a → #5B7E3C → #3d5828)`
- Tombol store: `bg-black rounded-xl`
- Phone mockup: `w-64 h-[500px] rounded-[3rem] border-[6px] border-[#333]`
- Floating badge: `bg-white rounded-2xl shadow-lg`

### Footer
- `bg-[#1C1C1C] text-white`
- Grid 4 kolom (`md:col-span-2` untuk brand)
- Email aksen: `text-[#FFD65A]`
- Border separator: `border-white/10`

---

## Layout Patterns

- **Full-page scroll:** satu halaman panjang, navigasi anchor `#section-id`
- **Section alternating background:** `#FAFAF5` ↔ `#FFFFFF` berselang-seling
- **Hero:** full-viewport `min-h-screen`, konten di bawah (`justify-end pb-20`)
- **Two-column:** `grid md:grid-cols-2 gap-16 items-center` (teks kiri, visual kanan)
- **Three-column cards:** `grid md:grid-cols-2 lg:grid-cols-3 gap-6`
- **Sidebar detail:** `lg:col-span-2` list + `lg:w-72` panel `sticky top-24`
- **Container:** `max-w-7xl mx-auto px-6`

---

## Icons

Inline SVG — tidak ada library ikon eksternal. Semua ikon menggunakan:
- `fill="none" stroke="currentColor"` (outline style)
- `strokeLinecap="round" strokeLinejoin="round" strokeWidth={2}`
- Ukuran: `w-4 h-4` inline tombol, `w-5 h-5` input prefix

Emoji digunakan untuk ilustrasi step card dan elemen dekoratif non-kritis.

---

## Interaktivitas

- **Hover tombol:** `hover:-translate-y-0.5 transition-all duration-200`
- **Hover kartu:** `hover:shadow-md transition-shadow duration-200`
- **Hover list item:** `hover:bg-[#5B7E3C]/5 hover:border-[#5B7E3C]/15`
- **Heatmap cell hover:** scale + shadow via `style` inline
- **Active nav:** `IntersectionObserver` threshold `0.3`
- **Search filter:** live filter dengan `useState`
- **Tab switcher:** `useState<"officers" | "reporters">`

---

## Animasi

- Pulse badge hero: `animate-pulse` (Tailwind built-in)
- Transisi umum: `transition-all duration-200` atau `duration-300`
- Progress bar fill: `transition-all duration-500` / `duration-700`
- Tidak ada animasi masuk (enter animation) atau scroll-triggered animation

---

## Responsivitas

| Breakpoint | Tailwind | Perubahan utama |
| ---------- | -------- | --------------- |
| Mobile     | default  | Stack vertikal, stat badge disembunyikan, phone mockup hidden |
| Tablet     | `md:`    | 2 kolom aktif, navbar full |
| Desktop    | `lg:`    | 3 kolom, sidebar heatmap muncul |

- Gunakan `hidden md:flex` / `flex md:hidden` untuk sembunyikan elemen responsif
- Font scale: `text-5xl md:text-7xl` untuk H1 hero
