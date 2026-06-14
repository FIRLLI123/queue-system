# PRD UI Dashboard — Sistem Antrian Customer Care
**Versi:** 1.0  
**Scope:** Spesifikasi desain visual, animasi, dan interaksi untuk seluruh halaman frontend

---

## 1. Design System

### 1.1 Color Palette

| Token | Hex | Penggunaan |
|---|---|---|
| `--primary` | `#2563EB` | Aksi utama, badge giliran, border aktif |
| `--primary-light` | `#EFF6FF` | Background card CC aktif |
| `--primary-glow` | `rgba(37,99,235,0.15)` | Shadow/glow efek CC pertama |
| `--success` | `#10B981` | Status online, completed |
| `--success-light` | `#ECFDF5` | Badge status online |
| `--warning` | `#F59E0B` | Menunggu, belum login |
| `--warning-light` | `#FFFBEB` | Background waiting state |
| `--danger` | `#EF4444` | Void, error, inactive |
| `--danger-light` | `#FEF2F2` | Background void/error |
| `--surface` | `#F8FAFC` | Background halaman |
| `--card` | `#FFFFFF` | Background card |
| `--border` | `#E2E8F0` | Border default |
| `--text-primary` | `#0F172A` | Heading utama |
| `--text-secondary` | `#64748B` | Label, keterangan |
| `--text-muted` | `#94A3B8` | Placeholder, timestamp |

### 1.2 Typography

| Role | Font | Weight | Size |
|---|---|---|---|
| Display (nama CC besar) | `Inter` | 700 | 32–40px |
| Heading card | `Inter` | 600 | 16–18px |
| Body | `Inter` | 400 | 14px |
| Data/angka besar | `Inter` | 800 | 36–48px (tabular-nums) |
| Badge/label kecil | `Inter` | 500 | 11–12px uppercase |
| Timestamp | `Inter Mono` | 400 | 12px |

### 1.3 Spacing & Shape

- Border radius card: `16px`
- Border radius button: `10px`
- Border radius badge: `999px` (pill)
- Shadow card default: `0 1px 3px rgba(0,0,0,0.06), 0 4px 16px rgba(0,0,0,0.04)`
- Shadow card hover: `0 4px 24px rgba(0,0,0,0.10)`
- Grid gap: `20px`

---

## 2. Motion & Animation System

Semua animasi mengikuti prinsip **purposeful motion** — animasi hanya ada jika memiliki makna informasi.

### 2.1 Queue Movement Animation (Signature Element)

Ini adalah elemen utama yang menjadi daya tarik visual dashboard.

**Saat CC menerima order:**

1. Card CC posisi #1 melakukan animasi **slide-out ke kiri + fade** (durasi 280ms, easing `cubic-bezier(0.4, 0, 0.2, 1)`)
2. Card-card di bawahnya melakukan animasi **slide-up smooth** secara berurutan dengan stagger 40ms per item
3. CC yang baru saja menerima order muncul di posisi terakhir dengan animasi **slide-in dari bawah + fade-in** (delay 200ms setelah step 1)
4. Card yang kini berada di posisi #1 mendapat **highlight pulse** sekali: border glow `--primary-glow` mengembang lalu mengecil (300ms)

**Spesifikasi CSS:**
```css
/* Kontainer antrian menggunakan layout yang mendukung animasi posisi */
.queue-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

/* Setiap item antrian menggunakan transisi pada transform + opacity */
.queue-item {
  transition: transform 280ms cubic-bezier(0.4, 0, 0.2, 1),
              opacity 280ms ease,
              box-shadow 200ms ease;
}

/* State animasi keluar (posisi #1 setelah klik terima) */
.queue-item.leaving {
  transform: translateX(-32px);
  opacity: 0;
}

/* State animasi masuk (posisi terakhir) */
.queue-item.entering {
  animation: slideInUp 300ms cubic-bezier(0.0, 0.0, 0.2, 1) forwards;
}

@keyframes slideInUp {
  from { transform: translateY(20px); opacity: 0; }
  to   { transform: translateY(0);    opacity: 1; }
}

/* Pulse highlight posisi pertama baru */
.queue-item.first-highlight {
  animation: firstPulse 600ms ease forwards;
}

@keyframes firstPulse {
  0%   { box-shadow: 0 0 0 0px rgba(37,99,235,0.3); }
  50%  { box-shadow: 0 0 0 6px rgba(37,99,235,0.15); }
  100% { box-shadow: 0 0 0 0px rgba(37,99,235,0); }
}
```

### 2.2 Animasi Lainnya

| Interaksi | Animasi | Durasi |
|---|---|---|
| Card muncul saat load | `fadeSlideUp` dari bawah, stagger per card | 200ms + 60ms/card |
| Hover card | `translateY(-2px)` + shadow naik | 150ms ease |
| Button hover | Background darkens + scale(1.01) | 120ms |
| Button click | scale(0.97) | 80ms |
| Badge status online | Dot berkedip gentle (pulse) | 2s infinite |
| Number counter (statistik) | Count-up dari 0 saat pertama load | 800ms ease-out |
| Aktivitas baru masuk | Slide-in dari atas, item lama geser turun | 250ms |
| Toast notifikasi | Slide-in dari kanan atas | 250ms ease |
| Modal void | Fade + scale(0.96 → 1) | 200ms |
| Skeleton loading | Shimmer horizontal | 1.5s infinite |

### 2.3 Reduced Motion

```css
@media (prefers-reduced-motion: reduce) {
  * {
    animation-duration: 1ms !important;
    transition-duration: 1ms !important;
  }
}
```

---

## 3. Layout Dashboard (Monitoring — Admin & CC)

### 3.1 Struktur Halaman

```
┌─────────────────────────────────────────────────────┐
│  NAVBAR  [Logo] [User Info] [Role Badge] [Logout]   │
├──────────────┬──────────────────────────────────────┤
│              │  Section 2: Giliran Sekarang          │
│  Section 1   │  ┌──────────────────────────────┐    │
│  Queue List  │  │  Avatar besar, nama CC,       │    │
│              │  │  status, waktu terakhir order  │    │
│  1. A1 NEXT  │  └──────────────────────────────┘    │
│  2. B1       ├──────────────────────────────────────┤
│  3. C1       │  Section 4: Statistik Hari Ini        │
│              │  ┌────┐ ┌────┐ ┌────┐ ┌────┐        │
│              │  │ WA │ │CALL│ │LIVE│ │TOT │        │
│              │  └────┘ └────┘ └────┘ └────┘        │
├──────────────┴──────────────────────────────────────┤
│  Section 3: Order Terakhir                           │
│  ┌───────────────────────────────────────────────┐  │
│  │  No Order | CC | Tipe | Status | Tgl | Jam    │  │
│  └───────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────┤
│  Section 5: Aktivitas Terakhir (live feed)          │
│  ○ 10:25  A1  VOID order #ORD-001                  │
│  ○ 10:21  C1  menerima LIVE                        │
└─────────────────────────────────────────────────────┘
```

### 3.2 Responsive Breakpoint

| Breakpoint | Perilaku |
|---|---|
| ≥ 1280px | 2 kolom (queue list kiri, konten kanan) |
| 768–1279px | 1 kolom, semua section stack vertikal |
| < 768px | Full mobile, card penuh lebar |

---

## 4. Komponen: Queue List (Section 1)

### 4.1 Container

- Width: `320px` (desktop), full width (mobile)
- Background: `--card`
- Border radius: `16px`
- Padding: `20px`
- Sticky di sisi kiri saat scroll

### 4.2 Queue Item Card

**State: Posisi #1 (NEXT)**
```
┌────────────────────────────────────┐
│  [1]  ●  A1              [NEXT]    │
│       Terakhir: 10:15              │
└────────────────────────────────────┘
```
- Background: `--primary-light`
- Border-left: `3px solid --primary`
- Badge "NEXT": pill, `--primary`, putih
- Dot status: `--success`, pulse animation
- Font nama: `600`, `--text-primary`

**State: Posisi #2 dst.**
```
┌────────────────────────────────────┐
│  [2]     B1                        │
│       Terakhir: 10:10              │
└────────────────────────────────────┘
```
- Background: `--card`
- Border: `1px solid --border`
- Nomor posisi: `--text-muted`, `500`

**State: CC Offline**
```
┌────────────────────────────────────┐
│  [1]  ○  A1         [OFFLINE]      │  ← warning color
│       Belum login                  │
└────────────────────────────────────┘
```
- Background: `--warning-light`
- Border-left: `3px solid --warning`
- Badge "OFFLINE": `--warning`

### 4.3 Pesan Status Global

Di bawah queue list, muncul chip pesan:
```
⏳  Menunggu A1 menerima order
```
- Background: `--warning-light`
- Border: `1px solid --warning`
- Border radius: `8px`
- Padding: `10px 14px`
- Font: `13px`, `--warning` color
- Animasi: fade-in saat muncul

---

## 5. Komponen: Statistik Hari Ini (Section 4)

### 5.1 Stat Card

Empat card dalam satu baris:

```
┌──────────────┐
│   💬  WA      │
│               │
│     42        │
│  order hari   │
│   ini         │
└──────────────┘
```

- Angka besar: `48px`, `800` weight, `tabular-nums`
- Count-up animation saat pertama render
- Icon: emoji atau SVG icon sesuai tipe
- Hover: lift + shadow

**Warna aksen per tipe:**
- WA: `#10B981` (hijau)
- CALL: `#2563EB` (biru)
- LIVE: `#8B5CF6` (ungu)
- Total: `#0F172A` (hitam)

---

## 6. Komponen: Order Terakhir (Section 3)

### 6.1 Tabel

- Background: `--card`, rounded `16px`
- Header: `11px`, uppercase, `--text-muted`
- Row hover: background `#F8FAFC`
- Row animasi: baris terbaru slide-in dari atas saat order baru masuk

### 6.2 Status Badge

| Status | Style |
|---|---|
| COMPLETED | Pill hijau `--success-light`, teks `--success` |
| VOID | Pill merah `--danger-light`, teks `--danger` |

### 6.3 Kolom

| Kolom | Width | Keterangan |
|---|---|---|
| No. Order | 140px | Monospace, bold |
| CC | 100px | Nama + avatar kecil |
| Tipe | 80px | Badge berwarna |
| Status | 100px | Badge |
| Tanggal | 100px | Format DD/MM/YYYY |
| Jam | 80px | Format HH:MM:SS |

---

## 7. Komponen: Aktivitas Terakhir (Section 5)

### 7.1 Live Feed List

Setiap item:
```
○  10:25   A1 · VOID order #ORD-042   Customer batal
○  10:21   C1 · menerima LIVE
○  10:17   B1 · menerima CALL
```

- Dot kiri: warna sesuai aksi (hijau = terima, merah = void, abu = login)
- Timestamp: monospace, `--text-muted`
- Nama CC: `600`, `--text-primary`
- Aksi: `--text-secondary`
- Item terbaru: subtle highlight `--primary-light` yang fade hilang setelah 2 detik

### 7.2 Animasi Feed

Saat item baru masuk (dari AJAX polling):
1. Item baru muncul di paling atas dengan `slideInDown` + fade
2. Item lama geser turun smooth
3. Item paling bawah fade-out jika sudah > 10 item

---

## 8. Halaman CC — Terima & Void Order

### 8.1 Layout

```
┌─────────────────────────────────────────────────────┐
│  NAVBAR                                             │
├────────────────────┬────────────────────────────────┤
│  Queue Info        │  Form Terima Order             │
│  Posisi Anda: #1   │                                │
│  Status: GILIRAN   │  Pilih Tipe Order:             │
│                    │  ( ) WA  ( ) CALL  ( ) LIVE    │
│                    │                                │
│                    │  [ TERIMA ORDER ]              │
├────────────────────┴────────────────────────────────┤
│  Void Order Terakhir                                │
│  Order #ORD-042 · LIVE · 10:15                     │
│  [ VOID ORDER ]                                     │
└─────────────────────────────────────────────────────┘
```

### 8.2 Posisi Info Card

**Giliran Anda (posisi #1):**
- Background: gradient subtle `--primary-light`
- Border: `2px solid --primary`
- Badge "GILIRAN ANDA": `--primary`, pill
- Teks: "Silakan terima order berikutnya"

**Bukan giliran (posisi #2+):**
- Background: `--surface`
- Border: `1px solid --border`
- Badge "POSISI #N": abu-abu
- Teks: "Menunggu giliran..."
- Progress bar visual posisi (opsional): strip horizontal menunjukkan posisi dalam antrian

### 8.3 Radio Button Tipe Order

Bukan radio button native — gunakan **toggle card custom**:

```
┌──────┐  ┌──────┐  ┌──────┐
│  💬  │  │  📞  │  │  📺  │
│  WA  │  │ CALL │  │ LIVE │
└──────┘  └──────┘  └──────┘
```

- Default: border `--border`, background `--card`
- Selected: border `--primary`, background `--primary-light`, checkmark kecil di pojok
- Hover: `translateY(-1px)`, border `--primary` light
- Transisi: `150ms ease`

### 8.4 Button Terima Order

**State Aktif (giliran #1):**
- Background: `--primary`
- Teks: "Terima Order" putih
- Width: full
- Height: `52px`
- Font: `16px`, `600`
- Hover: `darken(--primary, 8%)`
- Click: scale `0.97`, durasi `80ms`
- Setelah klik: loading spinner dalam button, teks berubah "Memproses..."

**State Disabled (bukan giliran):**
- Background: `--border`
- Teks: `--text-muted`, "Belum Giliran Anda"
- Cursor: `not-allowed`
- Tidak ada hover effect

### 8.5 Void Section

Hanya muncul jika CC memiliki order terakhir yang bisa di-void.

**Card void:**
```
┌───────────────────────────────────────────┐
│  ⚠️  Order Terakhir                        │
│  #ORD-042  ·  LIVE  ·  Hari ini 10:15    │
│                                           │
│           [ VOID ORDER ]                  │
└───────────────────────────────────────────┘
```
- Background: `--danger-light`
- Border: `1px solid` `#FECACA`
- Button void: outline `--danger`, teks `--danger`
- Hover: fill `--danger`, teks putih

### 8.6 Modal Void

```
┌──────────────────────────────────────────┐
│  Void Order #ORD-042                  ×  │
│  ─────────────────────────────────────   │
│  Alasan Void *                           │
│  ┌──────────────────────────────────┐   │
│  │  ( ) Customer batal              │   │
│  │  ( ) Salah klik                  │   │
│  │  ( ) Data tidak valid            │   │
│  │  ( ) Duplicate order             │   │
│  └──────────────────────────────────┘   │
│                                          │
│  Alasan lainnya (opsional):              │
│  ┌──────────────────────────────────┐   │
│  │                                  │   │
│  └──────────────────────────────────┘   │
│                                          │
│       [Batal]    [Konfirmasi Void]       │
└──────────────────────────────────────────┘
```

**Animasi modal:**
- Backdrop: `fade-in`, `opacity 0 → 0.5`, `200ms`
- Dialog: `scale(0.95) → scale(1)` + `opacity 0 → 1`, `200ms cubic-bezier`
- Dismiss: reverse, `150ms`

**Alasan void:** radio button custom (pilihan preset) + textarea tambahan  
**Button konfirmasi:** merah solid, disabled jika belum pilih alasan  
**Button batal:** outline abu-abu

---

## 9. Navbar

### 9.1 Struktur

```
[ 🔵 CC Queue ]    ─────────────────    [ ● Online ]  [ A1  ▾ ]
```

- Logo kiri: dot `--primary` + teks "CC Queue"
- Kanan: status dot (hijau = online) + nama user + dropdown
- Dropdown: Ubah Password / Logout
- Background: `--card`
- Border-bottom: `1px solid --border`
- Height: `60px`

### 9.2 Status Dot

Dot `8px`, `--success`, dengan CSS animation `pulse` infinite gentle — menunjukkan koneksi AJAX aktif.

Jika koneksi AJAX gagal, dot berubah merah + tooltip "Koneksi terputus, mencoba ulang..."

---

## 10. Toast Notification

Muncul di pojok kanan atas, stack maksimal 3.

**Tipe:**
- **Success** (hijau): "Order berhasil diterima"
- **Error** (merah): "Gagal memproses order"
- **Info** (biru): "Giliran Anda sekarang!"

**Animasi:**
- Masuk: `slideInRight` dari `translateX(110%)` ke `translateX(0)`, `250ms`
- Dismiss: `slideOutRight`, `200ms`, auto-dismiss setelah 4 detik
- Progress bar bawah toast: mengecil selama 4 detik sebagai timer visual

---

## 11. Empty & Loading States

### 11.1 Skeleton Loading

Saat halaman pertama kali load sebelum data AJAX datang:

- Queue list: 3 skeleton card dengan shimmer
- Statistik: 4 skeleton angka
- Tabel: 5 skeleton baris

Shimmer: `background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%)`  
Animation: `shimmer 1.5s infinite`

### 11.2 Empty State

Jika belum ada order hari ini:
```
      📋
  Belum ada order
  hari ini. Antrian
  siap menerima.
```
- Ilustrasi SVG kecil atau emoji besar
- Teks `--text-secondary`

---

## 12. Halaman Admin — Kelola CC & Tipe Order

### 12.1 Layout

Menggunakan sidebar kiri + konten kanan (standard admin panel).

Sidebar:
- Dashboard / Monitor
- Kelola Akun CC
- Kelola Tipe Order

### 12.2 Tabel Kelola CC

- Aksi: Edit (ikon pensil), Nonaktifkan (toggle switch), Reset Password
- Toggle switch animasi smooth `200ms`
- Konfirmasi modal untuk nonaktifkan CC

### 12.3 Form Tambah/Edit

Menggunakan modal (bukan halaman baru) untuk UX yang lebih cepat.

---

## 13. Halaman Login

### 13.1 Layout

Full-page centered, tidak ada sidebar.

```
┌─────────────────────────────┐
│                             │
│   🔵 CC Queue System        │
│                             │
│   Username                  │
│   ┌─────────────────────┐  │
│   │                     │  │
│   └─────────────────────┘  │
│                             │
│   Password                  │
│   ┌─────────────────────┐  │
│   │                     │  │
│   └─────────────────────┘  │
│                             │
│   [ Masuk ]                 │
│                             │
└─────────────────────────────┘
```

- Background: `--surface` atau subtle gradient `#EFF6FF → #F8FAFC`
- Card login: `--card`, shadow medium, `max-width: 400px`
- Input focus: border `--primary`, subtle glow `box-shadow: 0 0 0 3px rgba(37,99,235,0.12)`
- Button masuk: `--primary`, full width

---

## 14. AJAX Polling Behavior & UX

**Interval:** 3 detik

**Saat data berubah dari polling:**
- Jika urutan queue berubah → trigger animasi queue movement (Section 2.1)
- Jika statistik berubah → angka count-up singkat ke nilai baru (300ms)
- Jika aktivitas baru → prepend ke feed dengan animasi slide
- Jika giliran jatuh ke user CC yang sedang login → tampilkan toast "Giliran Anda!" + (opsional) suara notif

**Saat polling gagal:**
- Setelah 3x gagal berturut-turut: tampilkan banner kuning di atas halaman "Koneksi bermasalah, mencoba ulang..."
- Dot status navbar berubah merah

---

## 15. Checklist Aksesibilitas & Performa

- [ ] Semua button punya `aria-label` yang deskriptif
- [ ] Disabled button punya `aria-disabled="true"` dan `title` tooltip
- [ ] Modal punya focus trap saat terbuka
- [ ] Warna tidak menjadi satu-satunya penanda informasi (selalu ada label/ikon)
- [ ] `prefers-reduced-motion` direspek (lihat Section 2.3)
- [ ] Loading state ada di semua aksi async
- [ ] Error state ada di semua form
- [ ] Keyboard navigable (Tab, Enter, Escape untuk modal)

---

## 16. File & Struktur Frontend

```
resources/
└── views/
    ├── layouts/
    │   ├── app.blade.php          (layout utama + navbar)
    │   └── auth.blade.php         (layout login)
    ├── dashboard/
    │   └── index.blade.php        (monitoring utama)
    ├── cc/
    │   └── index.blade.php        (halaman CC terima/void)
    ├── admin/
    │   ├── users/
    │   └── order-types/
    └── auth/
        └── login.blade.php

public/
└── css/
    ├── design-system.css          (token, variable)
    ├── components.css             (card, badge, button)
    ├── animations.css             (semua keyframe & transition)
    └── dashboard.css              (layout khusus dashboard)
└── js/
    ├── queue-animation.js         (logic animasi pergerakan antrian)
    ├── polling.js                 (AJAX polling + diff detector)
    └── toast.js                   (notifikasi toast)
```

---

*PRD ini merupakan spesifikasi desain dan interaksi. Implementasi teknis backend mengacu pada PRD sistem utama.*