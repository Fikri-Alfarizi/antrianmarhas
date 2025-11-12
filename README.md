# AntrianMarhas — Queue Management System

Sistem antrian digital modern dengan real-time display, audio announcements, operator dashboard, dan pusat kontrol terpusat. Built with Laravel 12, Pusher, Web Speech API, dan Vite.

## 📋 Quick Start

### Prerequisites
- PHP 8.4+
- Composer
- Node.js & npm
- MySQL

### Installation

```bash
# Clone & install
git clone https://github.com/Fikri-Alfarizi/antrianmarhas.git
cd antrianmarhas
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Build & cache
npm run build
php artisan config:clear
php artisan cache:clear

# Run migrations
php artisan migrate

# Start server
php artisan serve
```

Open: http://localhost:8000

---

## 🎯 Features

### 1. Real-time Queue Display
- WebSocket-based display updates via Pusher
- Polling fallback (AJAX every 5s) if WebSocket unavailable
- Multiple loket (counter) support dengan real-time status tracking
- Audio announcement system dengan Web Speech API & Google TTS fallback
- Responsive design untuk display di TV/monitor

### 2. Admin Panel
- **Dashboard** — Overview sistem dan statistik
- **Manajemen Layanan** — CRUD services
- **Manajemen Loket** — CRUD counters dengan status real-time
- **Manajemen Pengguna** — User & role management
- **Pengaturan Umum** — Konfigurasi dasar sistem
- **Pengaturan Lanjutan** — Audio settings, API keys, dll
- **Monitoring Antrian** — Daftar lengkap antrian dengan filter & search
- **Analytics & Reporting** — Statistik harian, chart performa, export laporan
- **Print History** — Riwayat cetak antrian, reprint support

### 3. Pusat Kontrol Pemanggilan (New in v4)
- **Real-time Control Center** untuk memanggil antrian dari satu dashboard
- View semua loket dengan antrian pending
- Instant call (panggil) antrian ke loket tertentu
- Mark as complete (selesai) langsung dari pusat kontrol
- Real-time data refresh tanpa reload halaman

### 4. Operator Dashboard (Petugas)
- Simple queue management interface
- Call antrian (PANGGIL)
- Mark as serving (LAYANI)
- Mark as complete (SELESAI)
- Cancel queue (BATALKAN)
- Close loket (TUTUP LOKET)
- Real-time status display

### 5. Audio Announcement System
- **Beep notification** (Web Audio API)
- **Web Speech API** (browser native TTS)
- **Google Translate TTS** (fallback untuk audio yang lebih natural)
- Multi-language support (Indonesian, English, Javanese, Sundanese, Malay)
- Customizable message templates dengan placeholder {nomor}, {lokasi}
- Volume control & language selection dari admin panel

### 6. Public Kiosk & Status Check
- **Kios Cetak Antrian** — Pengunjung ambil nomor antrian
- **Status Check** — Check status antrian via QR code / nomor
- Waiting time estimation
- Real-time display update

---

## 🏗️ Architecture

```
┌─────────────────────────────────────┐
│    OPERATOR DASHBOARD               │
│  (Login & PANGGIL ANTRIAN)          │
└──────────────┬──────────────────────┘
               │ broadcast()
               ↓
┌─────────────────────────────────────┐
│    LARAVEL BACKEND                  │
│  AntrianDipanggil Event             │
└──────────────┬──────────────────────┘
               │ Pusher Broadcasting
         ┌─────┴──────┐
         ↓            ↓
    ┌────────┐   ┌───────────┐
    │WebSocket  │Polling (5s)
    └────┬────┘ └─────┬─────┘
         └──────┬─────┘
                ↓
    ┌─────────────────────┐
    │ DISPLAY PAGE        │
    │ (Queue + Audio)     │
    └─────────────────────┘
```

---

## ⚙️ Configuration

### Environment Variables (.env)

**Broadcasting:**
```
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=2074916
PUSHER_APP_KEY=your_key
PUSHER_APP_SECRET=your_secret
PUSHER_HOST=api-ap1.pusher.com
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_CLUSTER=ap1
```

**Frontend (Vite):**
```
VITE_PUSHER_APP_KEY=${PUSHER_APP_KEY}
VITE_PUSHER_APP_CLUSTER=${PUSHER_CLUSTER}
VITE_PUSHER_HOST=${PUSHER_HOST}
VITE_PUSHER_PORT=${PUSHER_PORT}
VITE_PUSHER_SCHEME=${PUSHER_SCHEME}
```

After `.env` changes:
```bash
npm run build
php artisan config:clear
```

---

## 🎵 Audio System

### How It Works

When operator clicks **PANGGIL ANTRIAN**:

1. **Backend**: Queue status changes to "dipanggil", event broadcast
2. **Frontend (Display)**:
   - Detects queue status change (WebSocket or polling)
   - Plays beep notification (Web Audio)
   - Speaks announcement (Web Speech → Google TTS fallback)

### Message Format

Template with placeholders (admin configurable):
```
"Nomor {nomor} silakan menuju ke {lokasi}"
```

Example output:
```
"Nomor A 001 silakan menuju ke Loket 1"
```

### Languages Supported

**Current Version**: Indonesian (Bahasa Indonesia) Only

| Code | Language | Web Speech | Google TTS |
|------|----------|-----------|-----------|
| id | Indonesian | id-ID | id |

> **Note**: Sistem saat ini dikonfigurasi untuk **hanya menggunakan Bahasa Indonesia**. Semua audio announcements dan text-to-speech akan menggunakan bahasa Indonesia secara eksklusif. Jika diperlukan multi-bahasa, hubungi development team.

### Fallback Logic

1. Try Web Speech API (browser native) → If supported, use it
2. If Web Speech error → Fallback to Google Translate TTS
3. Volume & language read from `audio_settings` DB table

---

## 📊 Admin Panel

### Audio Settings
```
http://localhost:8000/admin/audio-settings
```

Configure:
- **Aktif** (Enable/Disable)
- **Volume** (0-100)
- **Bahasa** (Language: Indonesian/Bahasa Indonesia Only)
- **Format Pesan** (Message template)
- **Tipe Audio** (text-to-speech/audio-file)

Changes apply immediately after display page refresh.

> **⚠️ Important**: Audio language is locked to Indonesian (id) untuk konsistensi sistem.

---

## 🧪 Testing

### Test Pages (Dev Only)

**Broadcast Test:**
```
http://localhost:8000/test/broadcast
```

**Audio Test:**
```
http://localhost:8000/test/audio
```

### Console Debugging

Open DevTools (F12) on display page and look for `[AUDIO]` logs:

```javascript
[AUDIO] Playing announcement: A001 → Loket 1
[AUDIO] Trying Web Speech API (id-ID)...
[AUDIO] ✅ Speaking in id-ID...
[AUDIO] ✅ Web Speech completed

// Or fallback:
[AUDIO] Fallback to Google TTS...
[AUDIO] Google TTS announcement started
```

### Verify Audio Works

1. Admin Panel → Set Audio to aktif ✓
2. Display page → Hard refresh (Ctrl+Shift+R) ✓
3. Operator Dashboard → Click PANGGIL
4. Listen for beep + voice on display ✓
5. Check console logs for [AUDIO] traces ✓

---

## 🚀 Deployment

### Production Checklist

- [ ] Configure real Pusher credentials in `.env`
- [ ] Set `APP_ENV=production` and `APP_DEBUG=false`
- [ ] Run `npm run build` with production settings
- [ ] Set up HTTPS/SSL certificate
- [ ] Configure database backups
- [ ] Monitor error logs
- [ ] Test audio on target browsers (Chrome, Firefox, Safari)

### Deploy Steps

```bash
# Pull latest code
git pull origin main

# Install/update deps
composer install --no-dev
npm install --production
npm run build

# Clear caches
php artisan config:clear
php artisan cache:clear

# Run migrations if needed
php artisan migrate --force
```

---

## 🐛 Troubleshooting

### No Audio on Display

1. **Check if audio is enabled:**
   ```bash
   php artisan tinker
   > DB::table('audio_settings')->first()
   # Verify aktif = 1, volume > 0
   ```

2. **Hard refresh display page** (Ctrl+Shift+R)

3. **Check browser console** (F12):
   - Look for `[AUDIO]` messages
   - Check for any `[ERROR]` logs

4. **Browser audio settings:**
   - Ensure browser not muted
   - Check system volume
   - Test with `/test/audio` page

### WebSocket Connection Fails

- **Normal in local development** if Pusher not configured
- **Polling fallback active** — displays still update every 5s
- Check `.env` PUSHER_* settings for typos

### English Voice Not Playing

- Some browsers don't include English voice for Web Speech
- Code automatically falls back to Google TTS
- Check console for error logs

### Settings Not Applied

- Hard refresh display page after admin changes
- Clear browser cache (Ctrl+Shift+Delete)
- Run `php artisan config:clear`

---

## 📁 Project Structure

```
antrianmarhas/
├── app/
│   ├── Events/
│   │   └── AntrianDipanggil.php
│   ├── Http/Controllers/
│   │   ├── DisplayController.php
│   │   ├── Admin/AudioSettingController.php
│   │   └── Petugas/LoketPetugasController.php
│   └── Models/
│       ├── Antrian.php
│       ├── AudioSetting.php
│       ├── Loket.php
│       └── ...
├── resources/
│   ├── views/
│   │   ├── display/index.blade.php
│   │   ├── admin/audio-settings/
│   │   └── petugas/loket/
│   └── js/bootstrap.js
├── routes/
│   └── web.php
├── database/
│   ├── migrations/
│   └── seeders/
├── .env.example
├── README.md
└── package.json
```

---

## 🔄 Real-time Flow

### Without Pusher (Polling Mode)

```
Display Page
  ↓ Every 5 seconds
GET /display/data
  ↓
Check loket antrian status
  ↓
If status === 'dipanggil' & new
  ↓
Play audio + update display
```

### With Pusher (WebSocket Mode)

```
Operator clicks PANGGIL
  ↓
broadcast(new AntrianDipanggil(...))
  ↓
Pusher receives event
  ↓
WebSocket → Display page instantly
  ↓
Play audio + update display
```

---

## 🛠️ Development Commands

```bash
# Build frontend
npm run build

# Watch mode (live reload)
npm run dev

# Format code
npm run format

# Clear caches
php artisan config:clear
php artisan cache:clear

# Database
php artisan migrate
php artisan db:seed

# Tinker console
php artisan tinker
```

---

## 📝 Database Tables

Key tables:
- `audio_settings` — Audio configuration (aktif, volume, bahasa, format_pesan)
- `antrians` — Queue records
- `lokets` — Counter/booth information
- `layanans` — Services offered
- `users` — Operator & admin accounts

---

## 📞 Support

For issues or questions:
1. Check this README first
2. Review console logs (`[AUDIO]`, `[LOAD]`, `[ECHO]` tags)
3. Check `.env` configuration
4. Verify database schema with migrations
5. Test with `/test/audio` page

---

## 📄 License

This project is open source and available under the MIT License.

---

**Last Updated**: November 12, 2025
**Version**: 4.0+ (Bahasa Indonesia Only)
**Project Name**: antrianmarhas
**Language Lock**: Fixed to Indonesian (Bahasa Indonesia)
