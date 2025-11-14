# 📋 PANDUAN LENGKAP SETUP ANTRIAN MARHAS v6 - DARI NOLO HINGGA SIAP PAKAI

Panduan ini menjelaskan step-by-step cara setup aplikasi Antrian Marhas mulai dari installing prerequisites hingga production-ready. Cocok untuk yang baru pertama kali.

---

## 🎯 Bagian 1: MENGENAL APLIKASI

### Apa Itu Antrian Marhas?

Antrian Marhas adalah sistem digital untuk manajemen antrian di rumah sakit/klinik modern dengan teknologi terbaru.

**Komponen Utama:**

```
┌─────────────────────────────────────────────────────┐
│  PASIEN DI KIOSK (Ambil Nomor Antrian)             │
│  "Halo, saya mau daftar pemeriksaan"               │
└──────────────────┬──────────────────────────────────┘
                   │ Input data → database
                   ↓
┌─────────────────────────────────────────────────────┐
│  DISPLAY DI RUANG TUNGGU (Tampilan Nomor + Audio)  │
│  "NOMOR ANTRIAN A001 SILAKAN MENUJU KE RUANG 3"   │
│  (Dengan suara dari speaker)                        │
└──────────────────┬──────────────────────────────────┘
                   │ Websocket real-time
                   ↓
┌─────────────────────────────────────────────────────┐
│  PETUGAS DI LOKET (Panggil dan Layani Pasien)      │
│  Klik: "PANGGIL" → "LAYANI" → "SELESAI"           │
└──────────────────┬──────────────────────────────────┘
                   │ Update status
                   ↓
┌─────────────────────────────────────────────────────┐
│  ADMIN DASHBOARD (Monitor & Konfigurasi)           │
│  - Lihat statistik pasien                           │
│  - Atur audio/display                              │
│  - Manage loket dan layanan                        │
└─────────────────────────────────────────────────────┘
```

**Fitur Utama:**
- ✅ Kiosk antrian dengan cetak nomor
- ✅ Display real-time dengan audio announcement
- ✅ Dashboard petugas untuk panggil pasien
- ✅ Admin panel untuk konfigurasi
- ✅ Audio dalam Bahasa Indonesia
- ✅ Sistem monitoring aktivitas

---

## 🎯 Bagian 2: APA YANG PERLU DIINSTALL (PREREQUISITES)

Sebelum mulai, pastikan komputer sudah punya software ini. Ini seperti "pondasi rumah" - tanpa ini, aplikasi tidak bisa berjalan.

### 2.1 PHP 8.4+ (Bahasa Pemrograman Backend)

**Apa gunanya?** Bahasa yang dipakai Laravel untuk membuat logic aplikasi.

**Cara Install Windows:**
1. Download dari https://windows.php.net/download/
2. Pilih "Non Thread Safe" versi terbaru (misal: php-8.4.0-nts-Win32-x64)
3. Extract ke folder (misal: `C:\php`)
4. Rename `php.ini-production` jadi `php.ini`
5. Buka System Environment Variables:
   - Tekan `Win + R`, ketik `sysdm.cpl`, tekan Enter
   - Tab "Advanced" → "Environment Variables"
   - Tambahin path `C:\php` ke PATH
6. Cek dengan buka Command Prompt: `php -v`

```bash
# Output yang diharapkan:
PHP 8.4.0 (cli) (built: Nov 12 2024, 10:00:00) ( NTS )
```

**Atau pakai Laragon (Lebih Mudah):**
- Download Laragon dari https://laragon.org/
- Install → Sudah semua terpasang (PHP, MySQL, Node.js)

---

### 2.2 MySQL 8.0+ (Database)

**Apa gunanya?** Menyimpan semua data (pasien, antrian, user, dll).

**Cara Install Windows (Pakai Laragon):**
- Laragon sudah include MySQL

**Atau Manual:**
1. Download XAMPP dari https://www.apachefriends.org/
2. Install → MySQL sudah ikut

**Verifikasi:**
```bash
mysql -u root -p
# Jika muncul prompt password, OK

# Atau di Laragon:
# Menu Laragon → Database → MySQL Connected (tanda hijau)
```

---

### 2.3 Node.js & npm (Untuk Frontend Assets)

**Apa gunanya?** Mengcompile CSS dan JavaScript agar modern dan optimize.

**Cara Install:**
1. Download dari https://nodejs.org/ (pilih LTS versi terbaru)
2. Install dengan next-next-finish
3. Buka Command Prompt:

```bash
node -v      # Cek versi Node
npm -v       # Cek versi npm

# Output yang diharapkan:
# v20.10.0 (Node)
# 10.2.3 (npm)
```

---

### 2.4 Composer (Package Manager untuk PHP)

**Apa gunanya?** Download library PHP (Laravel, dll) otomatis.

**Cara Install:**
1. Download dari https://getcomposer.org/download/
2. Jalankan installer
3. Pilih PHP executable path (misal: `C:\php\php.exe`)
4. Buka Command Prompt:

```bash
composer -V   # Cek versi

# Output:
# Composer version 2.7.0 2024-11-06 10:00:00
```

---

### 2.5 Git (Version Control)

**Apa gunanya?** Download project dari GitHub dan manage perubahan code.

**Cara Install:**
1. Download dari https://git-scm.com/
2. Install dengan default settings
3. Buka Command Prompt:

```bash
git --version

# Output:
# git version 2.42.0.windows.1
```

---

## 🎯 Bagian 3: DOWNLOAD & SETUP PROJECT

### 3.1 Download Project dari GitHub

```bash
# Buka Command Prompt dan masuk ke folder project
# Misal: cd C:\Users\YourName\Documents

# Download project
git clone https://github.com/Fikri-Alfarizi/antrianmarhas.git
cd antrianmarhas

# Atau jika belum bisa git, download ZIP dari GitHub:
# https://github.com/Fikri-Alfarizi/antrianmarhas → Code → Download ZIP
# Extract, buka folder yang sudah di-extract
```

---

### 3.2 Copy File Environment

Environment file (`.env`) berisi konfigurasi penting seperti database, secret key, dll.

```bash
# Di Windows, buka Command Prompt di folder antrianmarhas:
copy .env.example .env

# Di Linux/Mac:
cp .env.example .env
```

Sekarang akan ada file `.env` baru.

---

### 3.3 Edit File `.env` - KONFIGURASI DATABASE & SECRET KEY

Buka file `.env` dengan text editor (Notepad++, VSCode, atau Notepad biasa):

```env
# ========================================
# APP CONFIGURATION
# ========================================
APP_NAME="Antrian Marhas"
APP_ENV=local              # local untuk development, production untuk live
APP_KEY=                   # KOSONG DULU - akan di-generate
APP_DEBUG=true             # true untuk lihat error, false untuk production
APP_URL=http://127.0.0.1:8000

# ========================================
# DATABASE CONFIGURATION
# ========================================
DB_CONNECTION=mysql
DB_HOST=127.0.0.1         # localhost
DB_PORT=3306              # default MySQL port
DB_DATABASE=antrianmarhas_db   # Nama database (boleh diubah)
DB_USERNAME=root          # Username MySQL (biasanya root)
DB_PASSWORD=              # Password MySQL (kosong jika default)

# ========================================
# BROADCASTING (Untuk real-time update)
# ========================================
BROADCAST_CONNECTION=pusher

# Jika punya Pusher account (opsional):
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=api-ap1.pusher.com
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_CLUSTER=ap1

# ========================================
# IMGBB (Untuk upload logo)
# ========================================
IMGBB_API_KEY=your_imgbb_api_key

# Dapatkan di: https://imgbb.com/ → Sign up → Settings → API Key
# Atau biarkan kosong, gunakan local storage
```

**Penjelasan Penting:**

| Konfigurasi | Penjelasan |
|---|---|
| `APP_NAME` | Nama aplikasi di browser title |
| `APP_ENV` | `local` = development, `production` = live |
| `APP_DEBUG` | `true` = lihat error detail, `false` = sembunyikan (aman) |
| `DB_HOST` | Alamat database (127.0.0.1 = localhost) |
| `DB_DATABASE` | Nama database yang akan dibuat |
| `DB_USERNAME` | User MySQL (default: root) |
| `DB_PASSWORD` | Password MySQL |

---

### 3.4 Generate APP_KEY

APP_KEY adalah secret key unik untuk aplikasi Anda. Dipakai untuk enkripsi data.

```bash
php artisan key:generate
```

**Output yang diharapkan:**
```
✓ Application key set successfully.
```

Sekarang `.env` akan ter-update dengan `APP_KEY=base64:xxxxx...`

---

## 🎯 Bagian 4: INSTALL DEPENDENCIES

Dependencies adalah library-library yang dipakai aplikasi. Ini seperti "buku referensi" untuk Laravel.

### 4.1 Install PHP Dependencies

```bash
composer install
```

**Apa yang terjadi?**
- Laravel akan download semua library yang dipakai
- Ini membuat folder `vendor/` yang isinya 10,000+ files
- Butuh waktu 2-5 menit tergantung internet

**Output:**
```
Loading composer repositories...
...
Generating optimized autoload files
```

---

### 4.2 Install Node Dependencies

```bash
npm install
```

**Apa yang terjadi?**
- Download library JavaScript (React, Vite, Tailwind, dll)
- Membuat folder `node_modules/`
- Butuh waktu 1-3 menit

**Output:**
```
added 500+ packages, and audited 600 packages in 2m
```

---

## 🎯 Bagian 5: SETUP DATABASE

Sekarang kita buat database MySQL dan import schema/structure.

### 5.1 Buat Database Baru

**Opsi A: Pakai Command Prompt (Mudah)**

```bash
mysql -u root -p
# Tekan Enter jika tidak ada password, atau ketik password

# Jalankan query:
CREATE DATABASE antrianmarhas_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

**Output:**
```
Query OK, 1 row affected (0.00 sec)
```

**Opsi B: Pakai PhpMyAdmin (GUI - Lebih Mudah)**

1. Buka http://localhost/phpmyadmin (kalau pakai XAMPP/Laragon)
2. Login (username: root, password: kosong atau sesuai setting)
3. Menu kiri → Klik "New"
4. Database name: `antrianmarhas_db`
5. Collation: `utf8mb4_unicode_ci`
6. Klik "Create"

---

### 5.2 Run Migration (Buat Table Otomatis)

Migration adalah file yang berisi perintah untuk membuat table database.

```bash
php artisan migrate
```

**Output:**
```
Migration table created successfully.

  Illuminate\Foundation\ComposerScripts: Clearing cached bootstrap files
  Caching bootstrap files
  Cache cleared successfully.

Migrating: 0001_01_01_000001_create_cache_table.php
Migrated:  0001_01_01_000001_create_cache_table.php (50.23ms)

Migrating: 0001_01_01_000002_create_jobs_table.php
Migrated:  0001_01_01_000002_create_jobs_table.php (100.45ms)

... (dan seterusnya)

✓ Finished: 15 migrations
```

**Apa yang terjadi?**
- Laravel membuat 15 table otomatis:
  - `users` (admin, petugas, operator)
  - `settings` (konfigurasi sistem)
  - `audio_settings` (audio config)
  - `lokets` (loket/ruangan)
  - `layanans` (jenis layanan)
  - `antrians` (data antrian pasien)
  - ... dan lainnya

**Verifikasi:**
```bash
# Lihat table yang sudah dibuat:
mysql -u root -p
USE antrianmarhas_db;
SHOW TABLES;
```

---

### 5.3 Run Seeder (Isi Data Dummy)

Seeder adalah file yang isi database dengan data testing.

```bash
php artisan db:seed
```

**Output:**
```
  Database\Seeders\SettingSeeder .............................. DONE
  Database\Seeders\LayananSeeder .............................. DONE
  Database\Seeders\LoketSeeder ................................ DONE
  Database\Seeders\UserSeeder ................................. DONE
  Database\Seeders\AntrianSeeder .............................. DONE
  Database\Seeders\AudioSettingSeeder ......................... DONE
```

**Data yang di-insert:**

| Table | Data |
|-------|------|
| users | 3 user: admin, operator, petugas |
| layanans | 3 layanan: Pendaftaran, Pemeriksaan, Resep |
| lokets | 4 loket: Ruang 1, 2, 3, 4 |
| antrians | 10 data antrian dummy |
| audio_settings | Default config: Bahasa Indonesia, Volume 80% |

---

## 🎯 Bagian 6: BUILD FRONTEND ASSETS

Frontend adalah CSS, JavaScript yang dipakai di browser.

### 6.1 Build untuk Development

```bash
npm run dev
```

**Output:**
```
VITE v5.2.0 ready in 523 ms

➜  Local:   http://localhost:5173/
```

**Apa yang terjadi?**
- Compile CSS (Tailwind)
- Compile JavaScript (React, Vue, dll)
- Start Vite dev server untuk auto-reload

**Biarkan terminal ini tetap terbuka!** Ini untuk hot-reload saat edit file.

---

### 6.2 Build untuk Production

```bash
npm run build
```

**Output:**
```
vite v5.2.0 building for production...
✓ 45 modules transformed.
dist/index.html                  0.45 kB │ gzip:  0.20 kB
dist/assets/index-xxxxx.js     150.23 kB │ gzip: 45.33 kB
dist/assets/style-yyyyy.css     25.34 kB │ gzip:  5.21 kB

✓ built in 2.34s
```

**Apa yang terjadi?**
- Optimize CSS & JS
- Minify (kurangi ukuran)
- Create file di folder `public/build/`

---

## 🎯 Bagian 7: SETUP STORAGE SYMLINK

Storage symlink membuat file dari `storage/app/public/` bisa diakses dari web.

Misal: Logo disimpan di `storage/app/public/logos/logo.png`, tapi diakses via `http://localhost:8000/storage/logos/logo.png`

```bash
php artisan storage:link
```

**Output:**
```
The [public/storage] directory has been successfully linked.
```

**Verifikasi:**
- Cek folder `public/storage` - seharusnya ada symlink

---

## 🎯 Bagian 8: START DEVELOPMENT SERVER

Sekarang saatnya jalankan aplikasi!

### 8.1 Terminal 1: Jalankan Laravel

```bash
php artisan serve
```

**Output:**
```
INFO  Server running on [http://127.0.0.1:8000].

Press Ctrl+C to quit
```

**Biarkan terminal ini tetap terbuka!**

---

### 8.2 Terminal 2: Jalankan Vite (untuk hot-reload)

**Buka terminal baru** (jangan close yang tadi):

```bash
npm run dev
```

**Output:**
```
VITE v5.2.0 ready in 523 ms

➜  Local:   http://localhost:5173/
```

**Biarkan ini juga tetap terbuka!**

Sekarang Anda punya 2 terminal berjalan.

---

## 🎯 Bagian 9: AKSES APLIKASI

Buka browser dan akses sesuai kebutuhan:

### 9.1 Admin Dashboard
```
URL: http://127.0.0.1:8000/admin
Username: admin@antrianmarhas.local
Password: password123
```

Apa bisa dilakukan:
- ✅ Monitor statistik pasien
- ✅ Manage user (tambah/edit/hapus)
- ✅ Setup audio (pilih bahasa, volume)
- ✅ Manage loket dan layanan
- ✅ Lihat activity log

### 9.2 Kiosk (Pasien Ambil Nomor)
```
URL: http://127.0.0.1:8000/kiosk
```

Cara pakai:
1. Pilih layanan yang diinginkan
2. Klik "Ambil Nomor Antrian"
3. Tercetak nomor antrian

### 9.3 Display (Tampil di Ruang Tunggu)
```
URL: http://127.0.0.1:8000/display
```

Cara pakai:
1. Buka di layar besar/TV
2. Akan menampilkan nomor antrian yang sedang dipanggil
3. Audio akan terdengar otomatis saat ada nomor baru

**Tips:** Klik display terlebih dahulu agar audio bisa aktif (browser policy)

### 9.4 Petugas Loket
```
URL: http://127.0.0.1:8000/petugas/loket
Username: petugas@antrianmarhas.local
Password: password123
```

Cara pakai:
1. Login
2. Klik "PANGGIL" untuk call nomor berikutnya
3. Klik "MULAI LAYANI" saat melayani
4. Klik "SELESAI" saat selesai

---

## 🎯 Bagian 10: TEST ALUR LENGKAP

Coba alur dari awal sampai akhir:

### Step 1: Pasien Ambil Nomor (Kiosk)
1. Buka http://127.0.0.1:8000/kiosk
2. Pilih layanan "Pemeriksaan"
3. Klik "Ambil Nomor"
4. Catat nomor yang tercetak (misal: A001)

### Step 2: Lihat di Display
1. Buka http://127.0.0.1:8000/display (di layar lain atau tab lain)
2. Akan muncul "Nomor: A001"
3. Klik display untuk enable audio

### Step 3: Petugas Panggil Nomor
1. Login ke http://127.0.0.1:8000/petugas/loket
2. Klik "PANGGIL"
3. Display akan update dan menampilkan "A001 → RUANG 1"
4. Audio akan terdengar: "Nomor antrian A 001 silakan menuju ke Ruang 1"
5. Klik "MULAI LAYANI"
6. Setelah selesai, klik "SELESAI"

---

## 🎯 Bagian 11: KONFIGURASI AWAL DI ADMIN PANEL

Setelah sukses akses, setup konfigurasi dasar:

### 11.1 Pengaturan Umum
1. Login ke Admin → Menu "Pengaturan Umum"
2. Update:
   - **Nama Instansi**: Ganti dari "RSUD Marhas" ke nama RS/klinik Anda
   - **Logo**: Upload logo RS Anda
   - **Nomor Telepon**: Kontak RS Anda
   - **Alamat**: Alamat RS Anda
   - **Deskripsi**: Deskripsi singkat
3. Klik "Simpan"

### 11.2 Audio Settings
1. Menu "Advanced Settings" → "Audio Settings"
2. Setup:
   - **Aktif**: Pastikan checkbox ON ✓
   - **Volume**: Atur (80 recommended)
   - **Bahasa**: Pilih (Indonesia recommended)
   - **Format Pesan**: Edit template pesan
     - Default: `"Nomor antrian {nomor} silakan menuju ke {lokasi}"`
     - Misal: `"Pasien {nomor} silakan datang ke {lokasi}"`
3. Klik "Simpan"

### 11.3 Manajemen Loket
1. Menu "Manajemen Loket"
2. Lihat loket yang sudah ada (Ruang 1-4)
3. Bisa tambah/edit/hapus sesuai kebutuhan RS Anda

### 11.4 Manajemen Layanan
1. Menu "Manajemen Layanan"
2. Lihat layanan yang ada (Pendaftaran, Pemeriksaan, Resep)
3. Bisa hubungkan layanan ke loket tertentu

---

## 🎯 Bagian 12: USEFUL COMMANDS

Command-command yang sering dipakai:

```bash
# ===== DEVELOPMENT =====
php artisan serve              # Jalankan server Laravel
npm run dev                    # Jalankan Vite dev server

# ===== DATABASE =====
php artisan migrate            # Jalankan migration
php artisan db:seed            # Jalankan seeder
php artisan migrate:fresh --seed    # Reset DB + seed ulang

# ===== RESET SETTINGS =====
php artisan db:reset-keep-settings  # Reset DB tapi preserve logo & audio

# ===== CACHE & CONFIG =====
php artisan cache:clear       # Bersihkan cache
php artisan config:clear      # Bersihkan config cache
php artisan route:cache       # Cache routes (production)

# ===== STORAGE =====
php artisan storage:link      # Buat symlink untuk public storage

# ===== GENERATE BARU =====
php artisan make:model User                    # Buat model baru
php artisan make:controller Admin/UserController  # Buat controller
php artisan make:migration create_users_table # Buat migration

# ===== TINKER (Debug Console) =====
php artisan tinker            # Masuk ke console interaktif

# Di tinker:
> DB::table('settings')->first()        # Lihat data settings
> User::all()                           # Lihat semua user
> User::create(['name' => 'Budi', 'email' => 'budi@test.com', 'password' => bcrypt('pass123')])  # Buat user baru
> exit                                  # Keluar
```

---

## 🎯 Bagian 13: TROUBLESHOOTING

### Error: "SQLSTATE[HY000]: General error: 1030"
```
Penyebab: Database belum terbuat atau koneksi error
Solusi:
  1. Cek .env - apakah DB_HOST, DB_USERNAME, DB_PASSWORD benar?
  2. Pastikan MySQL sudah running
  3. Jalankan: php artisan cache:clear
  4. Jalankan: php artisan migrate:fresh --seed
```

### Error: "Unable to locate file in Vite manifest"
```
Penyebab: Asset belum di-compile
Solusi:
  npm run build
  php artisan config:clear
```

### Audio tidak terdengar di Display
```
Penyebab: Audio belum ter-enable, atau setting salah
Solusi:
  1. Klik display terlebih dahulu (browser policy)
  2. Cek di Admin → Audio Settings, pastikan Aktif = ON
  3. Buka browser DevTools (F12) dan lihat console untuk debug
  4. Refresh display (Ctrl+Shift+R)
```

### Logo tidak muncul
```
Penyebab: Symlink tidak valid atau permission error
Solusi:
  1. Jalankan: php artisan storage:link
  2. Refresh browser
  3. Cek apakah file ada di: storage/app/public/logos/
```

### Database tidak bisa connect
```
Penyebab: MySQL tidak running atau credential salah
Solusi:
  Windows:
    1. Buka Services (Windows + R → services.msc)
    2. Cari "MySQL80" atau "MySQL" → pastikan Running
    3. Atau gunakan Laragon → Klik "Start"
  
  Linux:
    sudo systemctl start mysql
```

### Port 8000 sudah dipakai
```
Penyebab: Port 8000 dipakai aplikasi lain
Solusi:
  Gunakan port berbeda:
  php artisan serve --port=8001
  
  Atau kill proses yang pakai port 8000:
  Windows: netstat -ano | findstr :8000
  Linux:   lsof -i :8000
```

---

## 🎯 Bagian 14: SIAP UNTUK PRODUCTION

Sebelum deploy ke server production, persiapkan ini:

### 14.1 Update .env untuk Production

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://antrian.rumahsakit.co.id

# Database production
DB_HOST=prod.db.server.com
DB_USERNAME=prod_user
DB_PASSWORD=VeryStrongPassword123!@#
DB_DATABASE=antrian_production

# Disable broadcasting untuk local development:
BROADCAST_CONNECTION=log
```

### 14.2 Generate Secret Key Baru

```bash
php artisan key:generate
```

### 14.3 Build Assets untuk Production

```bash
npm run build
```

### 14.4 Optimize untuk Production

```bash
php artisan config:cache
php artisan route:cache
php artisan optimize
```

### 14.5 Setup SSL/HTTPS

Gunakan Let's Encrypt (free):
- https://letsencrypt.org/

Atau pakai CloudFlare (free SSL):
- https://www.cloudflare.com/

---

## 🎯 Bagian 15: FILE-FILE PENTING UNTUK DISERAHKAN

Jika akan diserahkan ke orang lain, siapkan folder ini:

```
antrianmarhas-production/
│
├── README.md (panduan ini)
├── SETUP_GUIDE.md (file setup detail)
├── CREDENTIALS.txt (username/password default)
│
├── .env.example (JANGAN .env asli!)
│   (karena .env berisi secret key yang sensitive)
│
├── database/
│   └── antrianmarhas_backup.sql (backup database)
│
├── source-code/
│   └── semua folder project (kecuali node_modules & vendor)
│
└── PRODUCTION_CHECKLIST.md
   - Security checks
   - Backup strategy
   - Monitoring setup
```

**JANGAN serahkan:**
- ❌ `.env` asli (berisi APP_KEY, password DB, dll)
- ❌ `vendor/` folder (bisa di-download lagi dengan composer install)
- ❌ `node_modules/` folder (bisa di-download lagi dengan npm install)
- ❌ `storage/logs/` folder (berisi log sensitif)

---

## 🎯 Bagian 16: DUKUNGAN & REFERENSI

Jika ada error atau pertanyaan:

### Dokumentasi Official
- Laravel: https://laravel.com/docs
- Vite: https://vitejs.dev/
- Pusher: https://pusher.com/docs
- Tailwind CSS: https://tailwindcss.com/docs

### Repository GitHub
- https://github.com/Fikri-Alfarizi/antrianmarhas
- Issues: https://github.com/Fikri-Alfarizi/antrianmarhas/issues

### Forum Bantuan
- Laravel Indonesia: https://laravel.id/
- Stack Overflow: https://stackoverflow.com/
- GitHub Discussions

---

## ✅ CHECKLIST SETUP COMPLETE

Pastikan semua sudah done:

- [ ] PHP 8.4+ installed
- [ ] MySQL installed
- [ ] Node.js installed
- [ ] Composer installed
- [ ] Git installed
- [ ] Project di-clone
- [ ] `.env` file dibuat
- [ ] `APP_KEY` di-generate
- [ ] `composer install` selesai
- [ ] `npm install` selesai
- [ ] Database created
- [ ] Migration selesai
- [ ] Seeder selesai
- [ ] `npm run build` selesai
- [ ] `php artisan storage:link` selesai
- [ ] Laravel server running
- [ ] Vite dev server running
- [ ] Admin dashboard accessible
- [ ] Kiosk accessible
- [ ] Display accessible
- [ ] Petugas dashboard accessible
- [ ] Konfigurasi awal done

---

## 🎉 SELESAI!

Aplikasi Antrian Marhas sudah siap digunakan!

Untuk pertanyaan lebih lanjut atau perlu bantuan setup, silakan hubungi tim development.

**Happy Queue Management! 🚀**

---

**Last Updated:** November 13, 2025
**Version:** 4.1 Complete Setup Guide
**Created for:** Easy onboarding & troubleshooting
