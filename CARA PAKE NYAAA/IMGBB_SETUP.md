# Setup ImgBB untuk Logo/Gambar Online

## ✅ Status: API KEY SUDAH DIKONFIGURASI & DIOPTIMASI

API key ImgBB sudah tersimpan di `.env`:
```
IMGBB_API_KEY=a30d8b3502935d92899211958add3020
```

**Sistem sudah dioptimasi dengan:**
- ✅ Base64 encoding untuk upload yang reliable
- ✅ 120 detik timeout untuk koneksi lambat
- ✅ Smart fallback ke local storage jika ImgBB timeout
- ✅ Error logging yang detail

**Semuanya sudah siap!** Anda bisa langsung upload logo sekarang.

---

## Cara Upload Logo (Simple!)

## 🚀 Quick Start (Updated v2)

**Langkah-langkah:**

1. **Buka Pengaturan:**
   - `http://localhost/admin/pengaturan`

2. **Upload Logo:**
   - Pilih file gambar (JPEG, PNG, JPG, GIF, max 2MB)

3. **Isi Data Lainnya:**
   - Nama Instansi
   - Nomor Telepon
   - Alamat

4. **Klik "Simpan Pengaturan"**
   - Tunggu 2-5 detik
   - Jika berhasil: muncul notifikasi hijau
   - Jika error: akan tampil pesan error

5. **Verifikasi:**
   - ✅ Logo muncul di form preview
   - ✅ Refresh halaman → logo muncul di sidebar
   - ✅ Display page → logo muncul di header
   - ✅ Kios page → logo muncul di header

**Debugging (jika ada issue):**
- Buka: `http://localhost/test/logo-debug`
- Akan tampil status lengkap (URL, symlink, logs)

---

## 📝 Update Teknis (13 Nov 2025)

**Masalah yang diperbaiki:**
- ❌ Upload sebelumnya menggunakan multipart form yang tidak compatible
- ✅ Sekarang menggunakan base64 encoding (format yang ImgBB support)
- ✅ Timeout dipanjang dari 30s → 120s untuk koneksi lambat
- ✅ Jika ImgBB timeout, otomatis fallback ke local storage
- ✅ Fixed storage symlink 403 error
- ✅ Better error handling dan validation

**Smart Fallback System:**
```
Upload → Base64 Encode → Send to ImgBB
             ↓
        Sukses? → Save ImgBB URL ✅
             ↓
        Timeout/Error? → Fallback ke Local Storage ✅
```

**Hasil:**
- Gambar akan upload dengan benar ke ImgBB
- Atau fallback ke local storage jika ImgBB error
- Logo akan muncul di semua halaman
- Tidak ada lagi file text di ImgBB

---

## Gambar Dimana Saja

Logo yang sudah upload bisa diakses dari:
- ✅ Sidebar Admin (top-left)
- ✅ Display Real-time (header)
- ✅ Kios Cetak Antrian (header)
- ✅ Di manapun ada link database

---

## Troubleshooting

| Problem | Solusi |
|---------|--------|
| Logo tidak muncul | Refresh browser, tunggu 5 detik, check di Network tab browser DevTools |
| Error saat upload | Cek ukuran file (max 2MB) dan koneksi internet |
| API key error | API key sudah dikonfigurasi di .env |
| Gambar blur/pixelated | Upload gambar dengan resolusi tinggi (min 300x300px) |
| **Upload timeout** | Koneksi internet lambat. Tunggu 1-2 menit atau ulangi upload |
| **Upload gagal terus** | Cek di `storage/logs/laravel.log` untuk error detail |

---

## API Reference

Jika ingin extend/membuat fitur baru dengan ImgBB:

```php
// Usage dalam code:
$imgbbService = new ImgbbService();
$result = $imgbbService->upload($file, 'custom-name');

// Result array:
[
    'url' => 'https://i.ibb.co/...',        // URL display
    'thumb' => 'https://i.ibb.co/...',      // Thumbnail
    'delete_url' => 'https://ibb.co/...',   // Delete URL
    'id' => 'abc123'                         // Image ID
]
```

---

## Keuntungan ImgBB vs Storage Lokal

| Fitur | ImgBB ✅ | Storage Lokal ❌ |
|-------|---------|---------|
| Akses Online | Ya, CDN global | Localhost saja |
| Permanent | Ya, selamanya | Hilang jika delete |
| Backup Otomatis | Ya, ImgBB backup | Risiko kehilangan |
| Speed | Fast CDN | Tergantung server |
| Cost | Gratis unlimited | Free tapi limited |
| Setup | Super simple | Ribet |

---

## Tips

- 📸 Upload gambar PNG untuk logo (background transparan)
- 🎨 Gunakan resolusi minimum 300x300px
- ⚡ Gambar JPG lebih cepat di-render
- � Ganti logo kapan saja tanpa ribet
- � Semua history tersimpan otomatis

---

**Siap menggunakan? Buka `/admin/pengaturan` sekarang!** 🚀
