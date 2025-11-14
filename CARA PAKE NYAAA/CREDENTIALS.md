# 🔐 DEFAULT CREDENTIALS & QUICK REFERENCE

## 📋 Default Login Credentials

Setelah fresh install dengan `php artisan migrate:fresh --seed`, gunakan credentials berikut:

### Admin Dashboard
```
URL: http://127.0.0.1:8000/admin
Email: admin@antrianmarhas.local
Password: password123
```
**Akses:** Kelola semua sistem, setting audio, user management, dll.

### Petugas Loket
```
URL: http://127.0.0.1:8000/petugas/loket
Email: petugas@antrianmarhas.local
Password: password123
```
**Akses:** Panggil dan layani pasien di loket.

### Operator Kiosk
```
URL: http://127.0.0.1:8000/kiosk
Akses Publik (tidak perlu login)
```
**Akses:** Pasien ambil nomor antrian.

### Display
```
URL: http://127.0.0.1:8000/display
Akses Publik (tidak perlu login)
```
**Akses:** Tampilkan nomor antrian di ruang tunggu.

---

## 🔑 Important Secret Keys & Configuration

| Key | Value | Tempat |
|-----|-------|--------|
| APP_KEY | `base64:xxxxx...` | `.env` (auto-generated) |
| DB_PASSWORD | Sesuai setting MySQL | `.env` |
| IMGBB_API_KEY | (opsional) | `.env` |
| PUSHER_APP_KEY | (opsional) | `.env` |

---

## 📊 Database Information

| Komponen | Detail |
|----------|--------|
| Database Name | `antrianmarhas_db` |
| Username | `root` (default) |
| Password | Kosong atau sesuai setting |
| Host | `127.0.0.1:3306` |
| Port | `3306` |

---

## 📁 Database Tables

| Table | Fungsi |
|-------|--------|
| `users` | Admin, Petugas, Operator |
| `settings` | Konfigurasi sistem (logo, nama RS, dll) |
| `audio_settings` | Konfigurasi audio (bahasa, volume) |
| `lokets` | Loket/ruangan layanan |
| `layanans` | Jenis layanan (Pendaftaran, Pemeriksaan, Resep) |
| `antrians` | Data antrian pasien |
| `activity_logs` | Log aktivitas sistem |

---

## 🌐 URL & Akses

| Fungsi | URL | Tipe Akses |
|--------|-----|-----------|
| Admin Dashboard | http://127.0.0.1:8000/admin | Login Required |
| Kiosk | http://127.0.0.1:8000/kiosk | Public |
| Display | http://127.0.0.1:8000/display | Public |
| Petugas | http://127.0.0.1:8000/petugas/loket | Login Required |
| Laravel Server | http://127.0.0.1:8000 | Public |
| Vite Dev Server | http://127.0.0.1:5173 | Dev Only |

---

## ⚡ Quick Commands

```bash
# Start development
php artisan serve              # Terminal 1
npm run dev                    # Terminal 2

# Build production
npm run build

# Database
php artisan migrate            # Jalankan migrations
php artisan db:seed           # Isi data dummy
php artisan migrate:fresh --seed  # Reset semua

# Reset settings
php artisan db:reset-keep-settings  # Reset tapi keep logo

# Cache
php artisan cache:clear
php artisan config:clear

# Storage
php artisan storage:link
```

---

## 🔒 Security Checklist Production

Sebelum production:

- [ ] Ubah semua password default di Admin Panel
- [ ] Set `APP_DEBUG=false` di .env
- [ ] Set `APP_ENV=production` di .env
- [ ] Generate `APP_KEY` baru
- [ ] Setup SSL/HTTPS
- [ ] Backup `.env` secara aman
- [ ] Backup database
- [ ] Setup firewall
- [ ] Monitor logs secara berkala
- [ ] Setup automated backups

---

## 📞 Jika Ada Error

| Error | Penyebab | Solusi |
|-------|----------|--------|
| Database connection failed | MySQL tidak running | Start MySQL service |
| 500 Internal Server Error | APP_DEBUG=false | Set APP_DEBUG=true untuk lihat error |
| Vite manifest error | Asset belum compile | `npm run build` |
| Audio tidak dengar | Browser policy | Klik display sebelum play audio |
| Logo tidak muncul | Symlink error | `php artisan storage:link` |
| Port 8000 taken | Port sudah dipakai | `php artisan serve --port=8001` |

---

**Last Updated:** November 13, 2025
**Aplikasi:** Antrian Marhas v4.1
