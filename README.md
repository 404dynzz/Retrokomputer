# 🖥️ SISTEM POS RETRO KOMPUTER
*Web-Based Point of Sales & Inventory Management System berorientasi Retro Modern*

POS Retro Komputer adalah aplikasi manajemen kasir, transaksi, stok, dan laporan keuangan toko yang memadukan keindahan estetika retro modern (low-white, dark retro console) dengan keandalan teknologi modern yang cepat, modular, dan siap produksi (production-ready).

---

## 🛠️ Stack Teknologi & Arsitektur

Aplikasi ini dibangun menggunakan arsitektur modern Single Page Application (SPA) dengan memisahkan sisi Frontend dan Backend secara modular:

*   **Frontend**: Vue 3 (Composition API) + TypeScript + Tailwind CSS + Vite + Pinia (State Management)
*   **Backend**: Laravel 12 + Laravel Sanctum (Token-Based REST API Authentication)
*   **Database**: MySQL 8.0
*   **Containerization**: Docker & Docker Compose (Multi-stage builds)

---

## 👥 Peran Pengguna & Hak Akses (3 User Roles)

Sistem ini membagi wewenang pengguna secara ketat demi keamanan data operasional:

### 1. ⚙️ Admin (Administrator)
*   **Kelola Produk**: CRUD produk lengkap dengan kategori dan kode produk unik.
*   **Kelola Pembelian**: Input stok masuk dari supplier dengan pelacakan invoice.
*   **Kelola Retur**: Mengelola pengembalian barang pelanggan (penjualan) atau retur ke supplier (pembelian).
*   **Kelola Transaksi**: Melakukan penjualan di kasir POS dan memantau riwayat transaksi toko.
*   **Pengaturan Sistem (Eksklusif)**: Mengubah logo sistem (teks pixel/unggah gambar kustom), tinggi logo yang fleksibel (20px - 100px), serta tipe background halaman login.

### 2. 📊 Owner (Pemilik Toko)
*   **Dashboard Laba Rugi**: Menampilkan performa omset, HPP, kerugian barang, dan laba bersih secara real-time.
*   **Monitoring Transaksi**: Memantau grafik penjualan dan aktivitas kasir real-time.
*   **Kelola Inventaris Bermasalah**: Mencatat barang yang hilang/rusak untuk kalkulasi kerugian otomatis.
*   **Riwayat Stok**: Melacak keluar masuknya barang di toko secara mendalam.

### 3. 🛒 Kasir
*   **Kasir POS (Point of Sales)**: Pencarian cepat produk, scan barcode, keranjang transaksi interaktif, hitung subtotal otomatis, dan pengurangan stok real-time saat checkout.
*   **Cetak Nota**: Mencetak struk fisik atau ekspor PDF nota penjualan seketika.
*   **Riwayat Pribadi**: Memantau performa penjualan kasir yang bersangkutan.

---

## 🚀 Petunjuk Instalasi & Pengembangan Lokal

### Prasyarat
*   Docker & Docker Compose terinstal di komputer Anda, ATAU
*   PHP >= 8.2 + Composer + MySQL + Node.js >= 18 terinstal secara lokal (jika tidak menggunakan Docker).

### Opsi A: Menjalankan dengan Docker (Rekomendasi - Cepat & Praktis)
1.  Buka terminal di root directory proyek.
2.  Jalankan perintah berikut untuk membangun dan menghidupkan container:
    ```bash
    docker-compose up -d --build
    ```
3.  Jalankan migrasi database dan seed data awal di dalam container backend:
    ```bash
    docker-compose exec backend php artisan migrate --seed
    ```
4.  Hubungkan symbolic link untuk penyimpanan publik (agar file logo/background ter-upload dengan benar):
    ```bash
    docker-compose exec backend php artisan storage:link
    ```
5.  Aplikasi kini dapat diakses di:
    *   **Frontend**: `http://localhost:3000` (atau port dinamis yang tertera)
    *   **Backend API**: `http://localhost:8000`

### Opsi B: Instalasi Manual Lokal (Tanpa Docker)

#### 1. Setup Backend (Laravel 12)
1.  Masuk ke direktori backend: `cd backend`
2.  Instal dependensi PHP: `composer install`
3.  Salin konfigurasi lingkungan: `cp .env.example .env`
4.  Buat Application Key baru: `php artisan key:generate`
5.  Konfigurasikan database Anda di `.env` (misal: `DB_DATABASE=retrokomputer_db`).
6.  Jalankan migrasi database beserta seed data awal:
    ```bash
    php artisan migrate --seed
    ```
7.  Hubungkan symbolic link untuk penyimpanan media:
    ```bash
    php artisan storage:link
    ```
8.  Jalankan server pengembangan backend Laravel:
    ```bash
    php artisan serve
    ```

#### 2. Setup Frontend (Vue 3 + Vite)
1.  Masuk ke direktori frontend: `cd frontend`
2.  Instal dependensi Node.js: `npm install`
3.  Salin konfigurasi lingkungan: `cp .env.example .env` (pastikan `VITE_API_URL` mengarah ke server backend Anda).
4.  Jalankan server pengembangan frontend Vue:
    ```bash
    npm run dev
    ```
5.  Buka browser Anda dan akses alamat lokal yang ditampilkan di terminal (misal: `http://localhost:5173`).

---

## 🎨 Panduan Kustomisasi Branding & Warna Retro
Aplikasi ini menggunakan **Retro Dark Console Layout** (rendah warna putih untuk kenyamanan mata saat operasional panjang). Branding warna didasarkan pada logo retro komputer:
*   **Oranye Retro (`#FF7A00`)**: Digunakan untuk elemen tombol utama, hover, pixel-art border, dan notifikasi penting.
*   **Biru Retro (`#1D4ED8`)**: Digunakan untuk branding layout, sidebar highlights, dan elemen navigasi sekunder.
*   **Base Dark (`#0B0F19`) & Card Dark (`#131926`)**: Menjadi dominasi latar belakang visual agar terasa premium dan bersih dari cahaya putih yang silau.

*Catatan Format Logo:* Untuk hasil visual terbaik, disarankan mengunggah gambar logo dengan format **PNG transparan** agar menyatu mulus dengan latar gelap tanpa menyisakan kotak putih di tepinya.

---

## 🛡️ Standar Pemeliharaan Berkas (PENTING!)
Setiap kali selesai melakukan pengerjaan kode (coding), pastikan Anda memperbarui berkas dokumentasi internal berikut yang ada di root repositori:
1.  `DOKUMENTASI.md` (Dokumentasi sistem publik yang di-push ke GitHub).
2.  `.gemini-memory.md` / `.claude-memory.md` / `.chatgpt-memory.md` (Berkas memori state kecerdasan buatan, dikonfigurasi dalam `.gitignore` agar tetap tersembunyi dari GitHub publik).
