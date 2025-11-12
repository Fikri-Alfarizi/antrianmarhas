# TROUBLESHOOTING: Antrian Tidak Muncul di Petugas

## Diagnosa

Jika antrian berhasil dibuat di Kios tapi tidak muncul di halaman Petugas Loket, ikuti checklist ini:

### 1. Verifikasi Setup User & Loket

Akses URL berikut untuk diagnostic:
```
http://127.0.0.1:8000/petugas/diagnostics
```

Response JSON akan menampilkan:
- User ID, nama, role, dan `loket_id` yang ter-assign
- Loket ID, nama, dan `layanan_id` yang ter-assign
- Semua antrian menunggu hari ini
- Antrian yang sesuai dengan layanan loket

### 2. Checklist Setup

#### A. User Operator
- [ ] User dengan role `operator` sudah dibuat di Admin > Pengguna
- [ ] User ter-assign ke loket (field `loket_id` terisi)
- [ ] Lihat di Admin > Pengguna, cari user, pastikan `loket_id` bukan kosong

#### B. Loket
- [ ] Loket sudah dibuat di Admin > Loket
- [ ] Loket ter-assign ke layanan (field `layanan_id` terisi)
- [ ] Lihat di Admin > Loket, buka loket, pastikan `layanan_id` ter-set
- [ ] Status loket adalah "aktif"

#### C. Layanan
- [ ] Layanan sudah dibuat di Admin > Layanan
- [ ] Layanan berstatus "aktif"
- [ ] Loket di-assign ke layanan yang AKTIF

#### D. Antrian dari Kios
- [ ] Layanan di kios harus sama dengan layanan loket
- [ ] Antrian dibuat dengan `status` = "menunggu" dan `layanan_id` sesuai

### 3. Flow Untuk Muncul di Petugas

```
Kios (Cetak Nomor)
    ↓
    Antrian dibuat dengan layanan_id = X, status = "menunggu"
    ↓
Petugas Login (User dengan loket_id, Loket dengan layanan_id = X)
    ↓
    getAntrianList() cari: Antrian WHERE layanan_id = X AND status = "menunggu"
    ↓
Antrian muncul di "Daftar Menunggu"
```

### 4. Solusi Jika Masih Tidak Muncul

**A. Di halaman petugas, buka Developer Tools (F12) > Console**

Cek apakah URL_GET_LIST terhubung:
```javascript
// Di console, ketik:
fetch(URL_GET_LIST).then(r => r.json()).then(d => console.log(d))
```

Lihat response JSON. Jika `waiting` array kosong, berarti query tidak match.

**B. Verifikasi dengan Database**

```sql
-- Cek antrian menunggu hari ini
SELECT * FROM antrians 
WHERE status = 'menunggu' 
AND DATE(waktu_ambil) = CURDATE();

-- Cek loket dan layanan user
SELECT u.id, u.name, u.loket_id, l.id, l.layanan_id, l.nama_loket
FROM users u
LEFT JOIN lokets l ON u.loket_id = l.id
WHERE u.role = 'operator';
```

**C. Perbaikan Manual**

Jika belum ada user/loket/layanan setup:

1. Buka Admin Panel
2. Buat Layanan di "Admin > Layanan" (misal: "Pemeriksaan Umum")
3. Buat Loket di "Admin > Loket", pilih layanan yang dibuat di step 2
4. Buat User di "Admin > Pengguna" dengan:
   - Role: "operator"
   - Loket: pilih loket dari step 3
5. Login dengan user baru
6. Ke Kios, ambil nomor antrian dengan layanan yang sama
7. Di Petugas, nomor seharusnya muncul di "Daftar Menunggu"

### 5. Endpoint Debug

| Endpoint | Method | Deskripsi |
|----------|--------|-----------|
| `/petugas/diagnostics` | GET | Lihat status user, loket, layanan, dan antrian |
| `/petugas/loket/list` | GET | Fetch daftar antrian |
| `/kios/cetak` | POST | Buat antrian baru |

---

**Last Updated**: 2025-11-12  
**Status**: Active Troubleshooting
