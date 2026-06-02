# Dokumentasi Teknis — Retro Komputer POS

Dokumentasi ini ditujukan untuk programmer/developer yang akan melanjutkan atau memelihara proyek Retro Komputer POS System.

---

## 1. Arsitektur Sistem

```
┌─────────────────┐       ┌─────────────────┐
│   Frontend       │ ───▶  │   Backend        │
│   (Vue 3 + TS)   │ HTTP  │   (Laravel 11)   │
│   Port: 5173     │ REST  │   Port: 8000     │
└─────────────────┘       └─────────────────┘
                                   │
                                   ▼
                          ┌─────────────────┐
                          │   Database       │
                          │   (MySQL/SQLite) │
                          └─────────────────┘
```

- **Frontend**: Vue 3 + TypeScript + Vite + Tailwind CSS + Pinia (State Management)
- **Backend**: Laravel 11 + Sanctum (Auth Token) + Eloquent ORM
- **Komunikasi**: REST API via Axios, autentikasi menggunakan Bearer Token (Sanctum)

---

## 2. Struktur Folder

### Frontend (`/frontend`)

```
frontend/
├── public/                     # Static assets
├── src/
│   ├── assets/                 # CSS, images
│   ├── components/             # Reusable UI components
│   │   ├── icons/              # Icon components
│   │   └── __tests__/          # Component tests
│   ├── layouts/
│   │   └── MainLayout.vue      # Layout utama (sidebar + header + content area)
│   ├── pages/                  # Halaman-halaman aplikasi
│   │   ├── LoginPage.vue       # Halaman login
│   │   ├── DashboardAdmin.vue  # Dashboard untuk Admin & Kasir
│   │   ├── DashboardOwner.vue  # Dashboard untuk Owner
│   │   ├── ProdukList.vue      # Daftar produk (CRUD list)
│   │   ├── ProdukForm.vue      # Form tambah/edit produk
│   │   ├── POSPage.vue         # Halaman kasir POS (point of sale)
│   │   ├── TransaksiList.vue   # Riwayat transaksi (Admin: tabel, Owner: infografis)
│   │   ├── TransaksiDetail.vue # Detail satu transaksi
│   │   ├── PembelianList.vue   # Daftar pembelian dari supplier
│   │   ├── PembelianForm.vue   # Form tambah pembelian + upload struk
│   │   ├── SupplierList.vue    # CRUD supplier
│   │   ├── ReturList.vue       # Daftar retur + modal detail
│   │   ├── ReturForm.vue       # Form retur (pilih transaksi/pembelian + item)
│   │   ├── StokRiwayat.vue     # Riwayat mutasi stok (filter mingguan/bulanan/tahunan)
│   │   ├── BarangRusak.vue     # Catat barang rusak/hilang (Admin: form, Owner: infografis)
│   │   ├── LaporanPenjualan.vue# Laporan penjualan
│   │   ├── LaporanStok.vue     # Laporan stok
│   │   ├── LaporanLabaRugi.vue # Laporan laba rugi (Owner only)
│   │   └── SettingsPage.vue    # Pengaturan sistem (info saja, no upload)
│   ├── router/
│   │   └── index.ts            # Definisi routing + route guard (role-based)
│   ├── services/
│   │   ├── api.ts              # Axios instance (base URL + interceptor token)
│   │   └── index.ts            # Semua service API (auth, produk, transaksi, dll)
│   ├── stores/
│   │   ├── auth.ts             # Pinia store: autentikasi (login, logout, user state)
│   │   ├── cart.ts             # Pinia store: keranjang belanja POS
│   │   └── notifikasi.ts       # Pinia store: notifikasi
│   ├── types/
│   │   └── index.ts            # TypeScript interfaces & types
│   ├── App.vue                 # Root component
│   └── main.ts                 # Entry point (mount app, register plugins)
├── tailwind.config.js          # Konfigurasi Tailwind (warna retro custom)
├── vite.config.ts              # Konfigurasi Vite
└── package.json                # Dependencies
```

### Backend (`/backend`)

```
backend/
├── app/
│   ├── Http/
│   │   └── Controllers/        # API controllers
│   │       ├── AuthController.php
│   │       ├── ProdukController.php
│   │       ├── TransaksiController.php
│   │       ├── PembelianController.php
│   │       ├── ReturController.php
│   │       ├── LaporanController.php
│   │       ├── SettingController.php
│   │       ├── BarangRusakController.php
│   │       └── SupplierController.php
│   ├── Models/                 # Eloquent models
│   └── Providers/              # Service providers
├── config/                     # Laravel config files
├── database/
│   ├── migrations/             # Database migrations
│   └── seeders/                # Database seeders (demo data)
├── routes/
│   ├── api.php                 # API route definitions (semua endpoint)
│   ├── web.php                 # Web routes (minimal)
│   └── console.php             # Console commands
├── storage/                    # File uploads, logs, cache
├── .env                        # Environment variables (DB, APP_KEY, dll)
└── composer.json               # PHP dependencies
```

---

## 3. Alur Data (Data Flow)

### 3.1 Login Flow

```
LoginPage.vue
  ↓ submit form (email, password)
authStore.login()
  ↓ POST /api/login
AuthController@login
  ↓ validate credentials → generate Sanctum token
  ↓ return { access_token, user }
authStore
  ↓ simpan token + user ke localStorage
  ↓ redirect ke /dashboard atau /dashboard/owner
```

### 3.2 Transaksi POS Flow

```
POSPage.vue
  ↓ pilih produk → masukkan ke keranjang (cartStore)
  ↓ atur qty, pilih metode pembayaran
  ↓ klik "BAYAR & SELESAI"
cartStore.getPayload()
  ↓ POST /api/transaksi { items, metode_pembayaran }
TransaksiController@store
  ↓ validasi stok
  ↓ buat record Transaksi + TransaksiDetail
  ↓ kurangi stok produk
  ↓ catat RiwayatStok (tipe: keluar)
  ↓ return { kode_transaksi, total }
POSPage.vue
  ↓ tampilkan modal sukses
  ↓ clear keranjang
```

### 3.3 Pembelian Flow

```
PembelianForm.vue
  ↓ pilih supplier (dari dropdown)
  ↓ isi no invoice
  ↓ upload struk/bukti (opsional)
  ↓ tambah item: pilih produk, qty, harga beli
  ↓ submit
  POST /api/pembelian (FormData: supplier, invoice, items[], struk_file)
PembelianController@store
  ↓ buat record Pembelian + PembelianDetail
  ↓ tambah stok produk
  ↓ catat RiwayatStok (tipe: masuk)
  ↓ simpan file struk ke storage
```

### 3.4 Retur Flow

```
ReturForm.vue
  ↓ pilih jenis retur (penjualan/pembelian)
  ↓ pilih referensi transaksi/invoice (searchable dropdown)
  ↓ pilih item yang akan diretur + qty
  ↓ isi ongkir (opsional) + alasan
  ↓ submit
  POST /api/retur { jenis_retur, referensi_id, alasan, ongkir, items[] }
ReturController@store
  ↓ buat record Retur + ReturDetail
  ↓ kembalikan stok (jika retur penjualan: stok +)
  ↓ kurangi stok (jika retur pembelian: stok -)
  ↓ catat RiwayatStok
```

---

## 4. Role-Based Access Control

Sistem memiliki 3 role user:

| Fitur / Menu         | Admin | Kasir | Owner |
|----------------------|:-----:|:-----:|:-----:|
| Dashboard            |  ✅   |  ✅   |  ✅*  |
| Produk (CRUD)        |  ✅   |  👁   |  ❌   |
| Kasir POS            |  ❌   |  ✅   |  ❌   |
| Pembelian            |  ✅   |  ❌   |  ❌   |
| Supplier (CRUD)      |  ✅   |  ❌   |  ❌   |
| Retur                |  ✅   |  ❌   |  ❌   |
| Barang Rusak (Input) |  ✅   |  ❌   |  ❌   |
| Barang Rusak (View)  |  ✅   |  ❌   |  ✅** |
| Transaksi (Tabel)    |  ✅   |  ✅   |  ❌   |
| Transaksi (Diagram)  |  ❌   |  ❌   |  ✅   |
| Riwayat Stok         |  ✅   |  ❌   |  ✅   |
| Laporan Penjualan    |  ✅   |  ✅   |  ✅   |
| Laporan Stok         |  ✅   |  ❌   |  ✅   |
| Laporan Laba Rugi    |  ❌   |  ❌   |  ✅   |
| Pengaturan Sistem    |  ✅   |  ❌   |  ❌   |

- `*` Owner memiliki dashboard terpisah (DashboardOwner.vue)
- `**` Owner melihat infografis/diagram, bukan form input
- `👁` Kasir hanya bisa melihat (read-only)

Route guard diimplementasikan di `router/index.ts` via `meta.roles` dan `beforeEach`.

---

## 5. API Endpoints

Semua endpoint berada di prefix `/api`.

### Public
| Method | Path | Keterangan |
|--------|------|------------|
| POST | `/login` | Login dan mendapat token |
| GET | `/settings/active` | Ambil pengaturan aktif |

### Protected (require `auth:sanctum`)
| Method | Path | Controller | Keterangan |
|--------|------|------------|------------|
| POST | `/logout` | AuthController | Logout (revoke token) |
| GET | `/me` | AuthController | Data user yang login |
| GET/POST/PUT/DELETE | `/suppliers` | SupplierController | CRUD Supplier |
| GET/POST/PUT/DELETE | `/produk` | ProdukController | CRUD Produk |
| GET | `/transaksi` | TransaksiController | List transaksi |
| POST | `/transaksi` | TransaksiController | Buat transaksi baru |
| GET | `/transaksi/{id}` | TransaksiController | Detail transaksi |
| GET | `/pembelian` | PembelianController | List pembelian |
| POST | `/pembelian` | PembelianController | Buat pembelian baru |
| GET | `/pembelian/{id}` | PembelianController | Detail pembelian |
| GET | `/retur` | ReturController | List retur |
| POST | `/retur` | ReturController | Buat retur baru |
| GET | `/laporan/dashboard` | LaporanController | Data KPI dashboard |
| GET | `/laporan/stok` | LaporanController | Statistik stok |
| GET | `/riwayat-stok` | LaporanController | Riwayat mutasi stok |
| POST | `/settings` | SettingController | Update pengaturan |
| GET | `/barang-rusak` | BarangRusakController | List barang rusak |
| POST | `/barang-rusak` | BarangRusakController | Catat barang rusak |

---

## 6. State Management (Pinia Stores)

### `auth.ts` — Autentikasi
- `user`: Data user yang sedang login
- `token`: Bearer token (Sanctum)
- `login(email, password)`: Login dan simpan ke localStorage
- `logout()`: Revoke token + clear localStorage + redirect ke login
- `init()`: Load token/user dari localStorage saat app mount
- Computed: `isAdmin`, `isKasir`, `isOwner`, `isAuthenticated`

### `cart.ts` — Keranjang POS
- `items[]`: Array CartItem (produk + qty + subtotal)
- `metode_pembayaran`: tunai/debit/transfer
- `addItem(produk)`: Tambah ke keranjang (cek stok)
- `updateQty(produkId, qty)`: Update jumlah item
- `removeItem(produkId)`: Hapus item dari keranjang
- `clearCart()`: Kosongkan keranjang
- `getPayload()`: Format data untuk dikirim ke API

### `notifikasi.ts` — Notifikasi
- `list[]`: Daftar notifikasi
- `unreadCount`: Jumlah belum dibaca

---

## 7. Validasi Input (Frontend)

| Field | Validasi |
|-------|----------|
| Kode Produk | Alfanumerik, 4-5 karakter, tanpa karakter spesial |
| Nama Produk | Alfanumerik + spasi, tanpa karakter spesial |
| Harga (Beli/Jual) | Angka saja, maksimal 9 digit, otomatis format ribuan |
| Kategori | Alfanumerik + spasi, tanpa karakter spesial |
| Qty | Angka positif (min: 1) |
| Stok | Angka non-negatif |

---

## 8. Menjalankan Proyek

### Prerequisites
- Node.js >= 18
- PHP >= 8.2
- Composer
- MySQL atau SQLite

### Frontend
```bash
cd frontend
npm install
npm run dev         # Development server (port 5173)
npm run build       # Production build
```

### Backend
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve   # Development server (port 8000)
```

### Docker (Opsional)
```bash
docker-compose up -d
```

---

## 9. Konvensi Kode

- **Naming**: camelCase untuk variabel/fungsi JS, snake_case untuk PHP dan field database
- **Komponen Vue**: PascalCase (e.g., `ProdukForm.vue`)
- **Tipe**: Semua tipe didefinisikan di `types/index.ts`
- **Service**: Semua API call melalui `services/index.ts`, tidak langsung dari komponen
- **Store**: State global hanya melalui Pinia store (`stores/`)
- **Styling**: Tailwind CSS dengan warna custom retro (`retro-blue`, `retro-orange`, `retro-dark`, dll)
