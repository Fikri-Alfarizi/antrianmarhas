# 📦 ANTRIAN MARHAS v6 - PAKET LENGKAP UNTUK DEPLOY

Ini adalah paket lengkap aplikasi Antrian Marhas yang siap untuk dideploy ke server atau digunakan di lingkungan baru.

---

## 📄 File-File Panduan yang Tersedia

### 1. **README.md** (Technical Documentation)
   - Deskripsi teknis aplikasi
   - Fitur lengkap
   - Troubleshooting
   - Konfigurasi production

### 2. **SETUP_GUIDE.md** (📖 PANDUAN UTAMA - BACA INI DULU)
   - **16 bagian lengkap** dari nol hingga production
   - Penjelasan detail setiap step
   - Cocok untuk yang belum pernah setup Laravel
   - Termasuk troubleshooting common errors

### 3. **QUICK_START.md** (⚡ Setup Cepat 5 Menit)
   - Untuk yang sudah berpengalaman
   - Command essentials saja
   - Langsung ke hasilnya

### 4. **CREDENTIALS.md** (🔐 Referensi Cepat)
   - Default username & password
   - URL akses semua modul
   - Database information
   - Quick commands
   - Security checklist

---

## 🎯 CARA MEMULAI

### Opsi A: Pemula (Tidak Pernah Setup Laravel)
1. Baca **SETUP_GUIDE.md** dari awal sampai habis
2. Ikuti setiap step dengan cermat
3. Referensi **CREDENTIALS.md** jika lupa informasi
4. Tanya jika ada yang tidak mengerti

### Opsi B: Advanced (Sudah Berpengalaman)
1. Baca **QUICK_START.md**
2. Jalankan command-command di dalamnya
3. Referensi **CREDENTIALS.md** untuk detail

---

## 📊 Apa Itu Aplikasi Ini?

**Antrian Marhas** = Sistem manajemen antrian modern dengan:

```
PASIEN DI KIOSK
  ↓ Ambil nomor antrian
  ↓
DISPLAY DI RUANG TUNGGU  
  ↓ Tampil nomor + audio
  ↓
PETUGAS DI LOKET
  ↓ Panggil & layani pasien
  ↓
ADMIN DASHBOARD
  ↓ Monitor & settings
```

---

## ⚙️ Technology Stack

- **Backend:** Laravel 12 (PHP Framework)
- **Frontend:** Vite + Tailwind CSS
- **Database:** MySQL
- **Real-time:** Pusher WebSocket
- **Audio:** Web Speech API + Google TTS

---

## 🎬 Quick Access

Setelah setup selesai, akses aplikasi di:

| Modul | URL | Username | Password |
|-------|-----|----------|----------|
| Admin Dashboard | http://127.0.0.1:8000/admin | admin@antrianmarhas.local | password123 |
| Petugas Loket | http://127.0.0.1:8000/petugas/loket | petugas@antrianmarhas.local | password123 |
| Kiosk | http://127.0.0.1:8000/kiosk | (publik) | - |
| Display | http://127.0.0.1:8000/display | (publik) | - |

---

## 📋 Prerequisites (Yang Perlu Diinstall Dulu)

```
✅ PHP 8.4+
✅ MySQL 8.0+
✅ Node.js 18+
✅ Composer
✅ Git
```

Jika belum ada, ikuti instruksi di SETUP_GUIDE.md bagian 2.

---

## 🚀 Langkah Tercepat (Copy-Paste)

```bash
# 1. Clone project
git clone https://github.com/Fikri-Alfarizi/antrianmarhas.git
cd antrianmarhas

# 2. Setup
copy .env.example .env
php artisan key:generate

# 3. Database
mysql -u root -p
CREATE DATABASE antrianmarhas_db CHARACTER SET utf8mb4;
EXIT;

# 4. Install & migrate
composer install
npm install
php artisan migrate:fresh --seed
php artisan storage:link
npm run build

# 5. Run (2 terminal)
# Terminal 1:
php artisan serve

# Terminal 2:
npm run dev

# 6. Buka browser
http://127.0.0.1:8000/admin
```

**Done! Aplikasi sudah running.** ✅

---

## 🔐 PENTING - Jangan Bagikan

❌ **Jangan bagikan:**
- `.env` file asli (berisi secret key)
- `vendor/` folder
- `node_modules/` folder
- Database password ke orang yang tidak perlu tahu

---

## 📞 Jika Ada Pertanyaan

1. **Baca dulu:** SETUP_GUIDE.md (semua jawaban ada di sana)
2. **Referensi:** CREDENTIALS.md untuk quick info
3. **Troubleshooting:** README.md bagian Troubleshooting

---

## 📦 Struktur Folder

```
antrianmarhas/
├── README.md              # Technical docs
├── SETUP_GUIDE.md         # 📖 Panduan utama (BACA INI)
├── QUICK_START.md         # ⚡ Quick setup
├── CREDENTIALS.md         # 🔐 Reference
│
├── app/                   # Source code
├── routes/                # URL routing
├── database/              # Migrations & seeders
├── resources/             # Views & assets
├── storage/               # Files & logs
├── public/                # Web root
│
├── .env.example           # Env template
├── composer.json          # PHP dependencies
├── package.json           # Node dependencies
└── vite.config.js         # Build config
```

---

## ✅ Checklist Setup

- [ ] Prerequisites installed
- [ ] Project di-clone
- [ ] .env file created
- [ ] APP_KEY generated
- [ ] Database created
- [ ] composer install done
- [ ] npm install done
- [ ] Migration done
- [ ] Seeder done
- [ ] Storage link created
- [ ] Build done
- [ ] Laravel server running
- [ ] Vite server running
- [ ] Admin dashboard accessible
- [ ] Semua modul tested

---

## 🎉 Selamat!

Aplikasi Antrian Marhas siap digunakan! 🚀

---

**Dibuat:** November 13, 2025
**Versi:** 4.1
**Status:** Production Ready
**Lisensi:** MIT

Selamat setup! Jika ada pertanyaan, baca SETUP_GUIDE.md. 😊
