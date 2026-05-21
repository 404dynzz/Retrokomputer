# DOKUMENTASI POS RETRO KOMPUTER
*Sistem Point of Sales & Inventory Management Berorientasi Retro Modern*

Selamat datang di berkas dokumentasi resmi proyek **POS Retro Komputer**. Dokumentasi ini mencakup identitas sistem, peran pengguna, struktur basis data, konfigurasi skema warna retro modern, serta petunjuk pengembangan.

---

## 1. Identitas Proyek & Konsep

*   **Nama Proyek:** Sistem POS Retro Komputer
*   **Jenis Sistem:** Web-Based Point of Sales dan Inventory Management System
*   **Arsitektur:** Single Page Application (SPA) dengan pemisahan Frontend (Vue.js 3 + Vite + Tailwind CSS) dan Backend (Laravel 12 REST API + MySQL).
*   **Konsep Desain:** Retro Modern Computer. Antarmuka terinspirasi oleh konsol terminal klasik dan komputer lawas (low-white, dark retro console) yang dipadukan dengan performa modern yang cepat, aman, dan responsif.

---

## 2. Target Pengguna & Hak Akses (3 User Roles)

Sistem POS Retro Komputer dirancang untuk melayani **tiga kelompok pengguna** dengan wewenang yang berbeda secara spesifik:

### A. Admin
Memiliki wewenang penuh atas konfigurasi operasional dan data master sistem.
*   Autentikasi & Pengaturan Akun
*   Kelola Data Produk (CRUD, kategori, kode produk unik)
*   Kelola Pembelian Barang (Supplier & Stok Masuk)
*   Kelola Transaksi Penjualan & Cetak Nota
*   Kelola Retur (Penjualan & Pembelian)
*   Melihat Laporan Penjualan dasar dan pergerakan stok
*   **Akses Pengaturan Sistem:** Kustomisasi logo retro (teks/gambar), ukuran tinggi logo yang fleksibel, dan konfigurasi latar belakang login.

### B. Owner
Memiliki wewenang analitis penuh untuk memantau performa toko dan mengelola inventaris kritis.
*   Autentikasi & Pemantauan Dashboard Utama
*   Monitoring Transaksi Penjualan secara Real-time
*   Kelola Barang Hilang / Rusak (Inventaris bermasalah)
*   Melihat Laporan Laba Rugi Toko komprehensif (Pendapatan, HPP, Kerugian Inventaris, Laba Bersih)
*   Monitoring Riwayat Pergerakan Stok masuk-keluar secara mendalam

### C. Kasir
Memiliki fokus penuh pada pelayanan pelanggan dan aktivitas penjualan harian secara cepat.
*   Autentikasi & Kasir POS (Point of Sales)
*   Cari produk & Scan Barcode produk
*   Transaksi Penjualan (Keranjang, hitung subtotal otomatis, simpan transaksi)
*   Pengurangan stok otomatis setelah transaksi berhasil
*   Cetak nota / struk penjualan fisik atau PDF
*   Melihat riwayat transaksi penjualan pribadi

---

## 3. Skema Warna & Desain Branding (Retro Logo)

Desain warna disesuaikan langsung dengan **Logo Retro Komputer** resmi yang memadukan kehangatan oranye retro dan birunya swirl kosmik dengan latar belakang gelap yang nyaman di mata (rendah warna putih):

*   **Latar Belakang Utama:** `#0B0F19` (Deep Slate-Navy Gelap) — Mengurangi kelelahan mata kasir/admin.
*   **Kartu / Panel UI:** `#131926` (Retro Charcoal Blue) — Pengganti panel putih konvensional.
*   **Aksen Dominan (Logo Swirl):** `#1D4ED8` (Royal Blue Retro) — Dipakai untuk branding dan elemen interaktif sekunder.
*   **Highlight Utama (Huruf 'R' Logo):** Gradasi Oranye-Kuning (`#FF7A00` ke `#FFC857`) — Digunakan pada tombol utama, hover, status aktif, dan indikator penting.

### Kustomisasi Logo yang Fleksibel
Melalui menu **Pengaturan Sistem**, Admin dapat menyesuaikan logo:
1.  **Logo Teks:** Menampilkan karakter terminal retro pixel/font monospaced (`>_ Retro Komputer`).
2.  **Logo Gambar (Upload):** Menggunakan file gambar logo kustom.
    *   *Keterangan Format Gambar:* Sangat direkomendasikan menggunakan format **PNG dengan Latar Belakang Transparan** agar logo menyatu dengan indah tanpa garis kotak putih di atas latar belakang gelap aplikasi. Format alternatif yang didukung adalah JPG dan GIF (Maks. 2MB).
3.  **Ukuran Tinggi Logo (Fleksibel):** Admin dapat menentukan tinggi logo secara fleksibel (antara **20px hingga 100px**) menggunakan slider pengaturan yang interaktif. Ukuran ini akan langsung diterapkan secara real-time ke seluruh visual Header/Sidebar aplikasi.

---

## 4. Struktur Database Utama

### A. Tabel `users`
Menampung kredensial pengguna dan wewenang role (`admin`, `owner`, `kasir`).

### B. Tabel `produks` (Produk)
Menampung data produk beserta kategori dan stok pengaman.

### C. Tabel `transaksis` (Transaksi POS)
Menyimpan riwayat transaksi penjualan beserta detail item belanja (`detail_transaksis`).

### D. Tabel `pembelians` (Stok Masuk)
Mencatat invoice pembelian barang dari supplier untuk penambahan stok otomatis.

### E. Tabel `returs`
Menampung data retur produk cacat/salah kirim baik dari sisi pelanggan maupun supplier.

### F. Tabel `barang_rusaks`
Menampung pencatatan barang hilang/rusak sebagai variabel penentu laporan laba rugi.

### G. Tabel `settings`
Menyimpan pasangan key-value konfigurasi dinamis aplikasi, termasuk `retro_logo_type`, `retro_logo_text`, `retro_logo_url`, `retro_logo_height`, `login_background_type`, `login_background_color`, dan `login_background_url`.

---

## 5. Standar Pemeliharaan Berkas (PENTING!)

Untuk menjaga konsistensi pengerjaan tim AI dan Developer, harap patuhi aturan berikut:
1.  Setiap kali selesai melakukan pengerjaan kode (coding), **selalu perbarui file-file berikut** untuk mencatat progres dan memori terkini:
    *   `DOKUMENTASI.md` (Dokumentasi sistem publik)
    *   `.gemini-memory.md` (Memori agen Gemini)
    *   `.claude-memory.md` (Memori agen Claude)
    *   `.chatgpt-memory.md` (Memori agen ChatGPT)
2.  File `plan.txt`, `.gemini-memory.md`, `.claude-memory.md`, dan `.chatgpt-memory.md` diatur secara lokal melalui berkas `.gitignore` agar tidak di-push ke GitHub untuk menjaga kerahasiaan rancangan mentah AI. Hanya berkas `DOKUMENTASI.md` yang di-push secara publik di GitHub.
