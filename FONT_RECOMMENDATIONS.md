# Rekomendasi Font untuk Retrokomputer Web Application

## 📋 Font Stack yang Telah Diimplementasikan

Kami telah memperbarui konfigurasi font pada aplikasi Anda dengan stack yang optimal:

### 1. **Font Sans (Body Text) - Inter**

- **Penggunaan**: Teks utama, body, paragraf, labels
- **Alasan Pemilihan**:
  - Modern, clean, dan mudah dibaca di layar
  - Excellent readability pada ukuran kecil
  - Optimal untuk aplikasi bisnis modern
  - 4 weight varitas: 300 (light), 400 (regular), 500 (medium), 600 (semibold), 700 (bold)

### 2. **Font Display - Poppins**

- **Penggunaan**: Heading, title, dashboard names, emphasize text
- **Alasan Pemilihan**:
  - Bold dan eye-catching untuk judul
  - Geometric style yang modern
  - Weights: 600 (semibold), 700 (bold), 800 (extra bold)
  - Cocok untuk theme "retro-modern"

### 3. **Font Mono - JetBrains Mono**

- **Penggunaan**: Kode transaksi, reference numbers, technical data
- **Alasan Pemilihan**:
  - Perfect untuk menampilkan kode/ID
  - Tinggi readability untuk angka
  - Weights: 400 (regular), 500 (medium), 600 (semibold)

---

## 🎨 Font Pairing dan Best Practices

### Hierarki Teks yang Direkomendasikan

#### **Judul Halaman (Dashboard Admin/Kasir)**

```
Font: Poppins
Weight: 700 (Bold)
Size: 28px - 32px
Color: Slate-900 (#0f172a)
Line-height: 1.2
Letter-spacing: -0.5px
```

#### **Judul Section (Filter Data, Transaksi Terbaru)**

```
Font: Inter
Weight: 600 (Semibold)
Size: 14px
Color: Slate-800 (#1e293b)
Letter-spacing: 0.05em
Text-transform: Uppercase
```

#### **Body Text (Deskripsi, Sublabel)**

```
Font: Inter
Weight: 400 (Regular)
Size: 14px
Color: Slate-500 (#64748b)
Line-height: 1.5
```

#### **Kode Transaksi**

```
Font: JetBrains Mono
Weight: 600 (Semibold)
Size: 12px
Color: Retro-Blue (#1D4ED8)
```

---

## ✅ Implementasi Saat Ini

### Perubahan yang Telah Dilakukan:

1. ✅ **Tailwind Config Updated** (`frontend/tailwind.config.js`)
   - Ditambahkan font display untuk heading
   - Ditambahkan font mono untuk kode

2. ✅ **Google Fonts Integration** (`frontend/index.html`)
   - Preconnect ke Google Fonts untuk performa lebih cepat
   - Loading 3 font keluarga dengan optimal weights

3. ✅ **Dashboard Admin Layout Cleanup**
   - Improved header dengan visual indicator (garis orange)
   - Better spacing dan padding konsistensi
   - Removed empty icon spans
   - KPI cards dengan sizing lebih baik (text-3xl)
   - Improved filter badge styling
   - Better list header alignment

---

## 🎯 Tips Penggunaan Font di Kode

### Menerapkan Font Display untuk Judul:

```html
<h1 class="text-3xl font-bold text-slate-900 font-display">Dashboard Admin</h1>
```

### Menerapkan Font Mono untuk ID:

```html
<span class="text-xs font-mono text-retro-blue font-semibold">
  TRX-2024-001
</span>
```

### Menerapkan Font Sans Normal (Default):

```html
<p class="text-sm text-slate-500">Pantau operasional toko secara menyeluruh</p>
```

---

## 📱 Responsive Font Sizing

### Desktop View:

- Heading H1: 32px
- Heading H2: 24px
- Body: 14px
- Small: 12px

### Mobile View (Tailwind sm:):

- Heading H1: 28px → 32px
- Heading H2: 20px → 24px
- Body: 13px → 14px

---

## 🎨 Font Color Combinations (Recommended)

| Elemen        | Font           | Size | Weight | Color      | Usage                |
| ------------- | -------------- | ---- | ------ | ---------- | -------------------- |
| Heading Utama | Poppins        | 32px | 700    | slate-900  | Dashboard Title      |
| Section Title | Inter          | 14px | 600    | slate-800  | Filter, List headers |
| Description   | Inter          | 14px | 400    | slate-500  | Subtitle, labels     |
| Kode/ID       | JetBrains Mono | 12px | 600    | retro-blue | Transaction codes    |
| Badge Text    | Inter          | 11px | 600    | slate-600  | Status badges        |
| Success Text  | Inter          | 13px | 500    | green-600  | Success messages     |
| Error Text    | Inter          | 13px | 500    | red-600    | Error messages       |

---

## 🚀 Performance Tips

1. **Font Loading Strategy**:
   - Display swap (`font-display=swap`) digunakan untuk mencegah FOIT (Flash of Invisible Text)
   - Preconnect ke Google Fonts CDN untuk faster loading

2. **Subset Characters**:
   - Google Fonts otomatis melayani hanya karakter yang diperlukan
   - Khusus untuk bahasa Indonesia (Latin) sudah optimal

3. **Caching**:
   - Browser akan cache font Google secara otomatis
   - Subsequent page loads lebih cepat

---

## 💡 Rekomendasi Tambahan

### Untuk Konsistensi di Seluruh Aplikasi:

1. **Gunakan Font Utilities Tailwind**:

   ```html
   <!-- Untuk heading -->
   class="font-display font-bold text-3xl"

   <!-- Untuk mono (kode) -->
   class="font-mono text-xs font-semibold"

   <!-- Untuk text biasa -->
   class="font-sans text-sm"
   ```

2. **Letter Spacing untuk Uppercase**:

   ```html
   <h3 class="text-sm font-bold uppercase tracking-wider">Filter Data</h3>
   ```

3. **Line Height untuk Readability**:
   ```html
   <p class="text-sm leading-relaxed">
     Deskripsi panjang yang perlu readability baik
   </p>
   ```

---

## 📊 Font Fallback Chain

Jika Google Fonts gagal loading, browser akan menggunakan fallback:

```css
font-family:
  "Inter", "Segoe UI", "Helvetica Neue", "system-ui", "-apple-system",
  "sans-serif";
font-family: "Poppins", "Inter", "system-ui", "-apple-system", "sans-serif";
font-family: "JetBrains Mono", "Fira Code", "Courier New", "monospace";
```

Fallback ini memastikan aplikasi tetap dapat dibaca meski CDN Google Fonts tidak tersedia.

---

## ✨ Kesimpulan

Font stack yang telah diimplementasikan sudah optimal untuk:

- ✅ Readability tinggi
- ✅ Modern aesthetic yang cocok dengan Retro theme
- ✅ Performa loading yang baik
- ✅ Kompatibilitas cross-browser sempurna
- ✅ Mendukung typography hierarchy yang jelas

Aplikasi Anda sekarang memiliki typography yang profesional dan konsisten! 🎉
