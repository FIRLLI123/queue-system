# PRD - Sistem Antrian Customer Care (CC)

## 1. Overview

Sistem digunakan untuk mendistribusikan order secara adil kepada tim Customer Care (CC) menggunakan metode Round Robin Queue.

Terdapat 2 role:

### Admin

* Monitoring antrian
* Monitoring aktivitas order
* Kelola akun CC
* Kelola tipe order
* Tidak dapat menerima order
* Tidak dapat melakukan void

### CC

* Login ke sistem
* Melihat monitoring antrian
* Menerima order
* Memilih tipe order
* Melakukan void order terakhir
* Mengubah password sendiri

---

# 2. Business Rules

## 2.1 Queue System

Contoh urutan awal:

1. A1
2. B1
3. C1

Ketika A1 menerima order:

Urutan berubah menjadi:

1. B1
2. C1
3. A1

Ketika B1 menerima order:

1. C1
2. A1
3. B1

Sistem akan terus berputar menggunakan metode Round Robin.

---

## 2.2 Hak Terima Order

Hanya CC yang berada di posisi pertama yang dapat menerima order.

Contoh:

Queue saat ini:

1. A1
2. B1
3. C1

Maka:

A1 :

* Tombol aktif
* Bisa menerima order

B1 :

* Tombol disabled

C1 :

* Tombol disabled

---

## 2.3 CC Belum Login

Jika CC urutan pertama belum login:

Queue tetap menunggu.

Contoh:

1. A1
2. B1
3. C1

A1 belum login.

Maka:

* B1 tidak dapat menerima order
* C1 tidak dapat menerima order

Status monitor:

"Menunggu A1 menerima order"

---

## 2.4 Void Order

Void hanya boleh dilakukan oleh CC.

Admin tidak memiliki akses void.

Void hanya diperbolehkan pada:

* Order terakhir
* Belum ada order baru setelahnya

---

### Contoh

Queue awal:

1. A1
2. B1
3. C1

A1 menerima order.

Queue menjadi:

1. B1
2. C1
3. A1

A1 melakukan void.

Queue kembali menjadi:

1. A1
2. B1
3. C1

---

### Syarat Void

Wajib mengisi alasan.

Contoh:

* Customer batal
* Salah klik
* Data tidak valid
* Duplicate order

---

# 3. Realtime Monitoring

Monitoring harus otomatis update tanpa refresh browser.

Tahap awal:

AJAX Polling setiap 3 detik.

Tahap berikutnya (optional):

Websocket.

---

# 4. Dashboard Monitoring

## Section 1 - Queue Saat Ini

Menampilkan urutan CC aktif.

Contoh:

1. A1 (NEXT)
2. B1
3. C1

CC urutan pertama harus diberi warna berbeda.

---

## Section 2 - Informasi Giliran

Menampilkan:

* Nama CC yang sedang mendapat giliran
* Status aktif
* Waktu terakhir menerima order

---

## Section 3 - Order Terakhir

Menampilkan:

* Nomor order
* Nama CC
* Tipe order
* Status
* Tanggal
* Jam

---

## Section 4 - Statistik Hari Ini

Menampilkan:

* Total WA
* Total CALL
* Total LIVE
* Total Order

---

## Section 5 - Aktivitas Terakhir

Menampilkan histori terbaru:

Contoh:

10:15 A1 menerima WA

10:17 B1 menerima CALL

10:21 C1 menerima LIVE

10:25 A1 VOID order

---

# 5. Halaman CC

## Form Terima Order

Radio Button:

( ) WA

( ) CALL

( ) LIVE

Button:

[ TERIMA ORDER ]

---

## Validasi

Jika bukan urutan pertama:

Button disabled.

Pesan:

"Belum giliran Anda."

---

## Void

Menampilkan:

Order terakhir yang diterima.

Button:

[ VOID ORDER ]

Modal:

Alasan Void *

Wajib diisi.

---

# 6. Database Design

## users

| Field      | Type      |
| ---------- | --------- |
| id         | bigint    |
| name       | varchar   |
| username   | varchar   |
| password   | varchar   |
| role       | enum      |
| status     | enum      |
| created_at | timestamp |
| updated_at | timestamp |

Role:

* ADMIN
* CC

Status:

* ACTIVE
* INACTIVE

---

## order_types

Master tipe order.

| Field      | Type      |
| ---------- | --------- |
| id         | bigint    |
| name       | varchar   |
| status     | enum      |
| created_at | timestamp |
| updated_at | timestamp |

Contoh:

* WA
* CALL
* LIVE

---

## queue_positions

Posisi antrian aktif.

| Field        | Type      |
| ------------ | --------- |
| id           | bigint    |
| user_id      | bigint    |
| queue_number | integer   |
| status       | enum      |
| created_at   | timestamp |
| updated_at   | timestamp |

---

## orders

Riwayat penerimaan order.

| Field         | Type      |
| ------------- | --------- |
| id            | bigint    |
| order_number  | varchar   |
| user_id       | bigint    |
| order_type_id | bigint    |
| queue_before  | json      |
| queue_after   | json      |
| status        | enum      |
| void_reason   | text      |
| created_at    | timestamp |
| updated_at    | timestamp |

Status:

* COMPLETED
* VOID

---

## activity_logs

Audit log.

| Field       | Type      |
| ----------- | --------- |
| id          | bigint    |
| user_id     | bigint    |
| action      | varchar   |
| description | text      |
| created_at  | timestamp |
| updated_at  | timestamp |

Action:

* LOGIN
* ACCEPT_ORDER
* VOID_ORDER
* CHANGE_PASSWORD

---

# 7. UI/UX Guidelines

## Theme

Modern Dashboard.

Warna:

* Primary : #2563EB
* Success : #10B981
* Warning : #F59E0B
* Danger : #EF4444

---

## Framework

Laravel 8

Blade

Bootstrap 5

AdminLTE hanya sebagai base layout.

Monitoring dibuat custom fullscreen.

---

# 8. Security

* Password menggunakan bcrypt
* Middleware role
* Hanya CC urutan pertama yang dapat submit order
* Void wajib alasan
* Semua aktivitas masuk audit log

---

# 9. Future Enhancement

* Websocket realtime
* Multi shift
* Multi team CC
* Export Excel
* Dashboard TV mode
* Rekap harian otomatis
* Notifikasi suara ketika queue berubah
