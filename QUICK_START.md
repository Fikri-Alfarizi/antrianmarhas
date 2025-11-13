# ⚡ QUICK START (5 Menit Setup)

Panduan singkat untuk setup aplikasi dalam 5 menit!

---

## ✅ Prerequisites Sudah Terpasang?

Pastikan installed:
- ✅ PHP 8.4+
- ✅ MySQL running
- ✅ Node.js
- ✅ Composer
- ✅ Git

---

## 🚀 Step-by-Step (5 Menit)

### 1️⃣ Download Project (1 menit)

```bash
git clone https://github.com/Fikri-Alfarizi/antrianmarhas.git
cd antrianmarhas
```

---

### 2️⃣ Setup Environment (30 detik)

```bash
copy .env.example .env
php artisan key:generate
```

---

### 3️⃣ Create Database (30 detik)

```bash
mysql -u root -p
CREATE DATABASE antrianmarhas_db CHARACTER SET utf8mb4;
EXIT;
```

---

### 4️⃣ Install Dependencies (2 menit)

```bash
composer install
npm install
```

---

### 5️⃣ Setup Database & Assets (1 menit)

```bash
php artisan migrate:fresh --seed
php artisan storage:link
npm run build
```

---

## 🎯 Start Development

**Terminal 1:**
```bash
php artisan serve
```

**Terminal 2:**
```bash
npm run dev
```

---

## 🌐 Access

| Akses | URL | Username | Password |
|-------|-----|----------|----------|
| Admin | http://127.0.0.1:8000/admin | admin@antrianmarhas.local | password123 |
| Petugas | http://127.0.0.1:8000/petugas/loket | petugas@antrianmarhas.local | password123 |
| Kiosk | http://127.0.0.1:8000/kiosk | (publik) | - |
| Display | http://127.0.0.1:8000/display | (publik) | - |

---

## ✅ Done!

Aplikasi sudah siap! Untuk setup detail, lihat `SETUP_GUIDE.md`.

---

**Total waktu: ~5 menit** ⏱️
