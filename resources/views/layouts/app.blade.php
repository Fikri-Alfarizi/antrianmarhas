<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ruang Coding Antrian Admin | @yield('title')</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        /* RESET DAN BASE */
        *, *::before, *::after {
            box-sizing: border-box;
        }
        
        body {
            margin: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f8fafc; /* Latar belakang utama */
            color: #1e293b;
            font-size: 14px;
            min-height: 100vh;
            display: flex;
        }

        /* --- SIDEBAR MODERN (DIMODIFIKASI) --- */
        .sidebar {
            width: 260px;
            background: #f8fafc; /* REVISI 1: Warna disamakan dengan body */
            /* border-right: 1px solid #e2e8f0; */ /* REVISI 1: Pembatas dihapus */
            padding: 24px 16px;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease-in-out;
        }

        .brand {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 32px;
            padding: 0 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand i {
            color: #3b82f6;
            font-size: 24px;
        }

        .brand img {
            height: 28px;
            width: auto;
            max-width: 40px;
            object-fit: contain;
        }

        .nav-section {
            margin-bottom: 28px;
        }

        .nav-label {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            padding: 0 12px;
        }

        .nav-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .nav-list li {
            margin-bottom: 2px;
        }

        .nav-list a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 10px;
            color: #475569;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
        }

        .nav-list a:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .nav-list a.active {
            background: #3b82f6;
            color: white;
            font-weight: 600;
        }

        .nav-list .icon {
            width: 20px;
            text-align: center;
            font-size: 16px;
        }
        /* --- AKHIR SIDEBAR --- */

        /* --- STYLING TOMBOL LOGOUT BARU --- */
        .logout-button {
            /* Meniru .nav-list a */
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 10px;
            color: #475569;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            text-decoration: none;
            
            /* Reset style button */
            background: none;
            border: none;
            width: 100%;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            text-align: left;
        }
        .logout-button .icon {
            width: 20px;
            text-align: center;
            font-size: 16px;
        }
        .logout-button:hover {
            /* Style hover berbahaya (merah) */
            background: #fee2e2;
            color: #dc2626;
        }

        /* --- HEADER TOP (DIHAPUS) --- */

        /* MAIN CONTAINER */
        .main-container {
            margin-left: 260px; 
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            transition: margin-left 0.3s ease-in-out;
        }

        /* MAIN CONTENT SECTION */
        .main-content {
            flex: 1;
            padding: 32px;
            max-width: 1600px;
            width: 100%;
        }

        /* CARD & UTILITIES */
        .card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            padding: 24px;
            margin-bottom: 24px; /* Default margin untuk card */
        }
        
        /* RESPONSIVENESS */
        .menu-toggle { 
            display: none; 
            background: #3b82f6; 
            color: white; 
            border: none; 
            padding: 10px 15px; 
            cursor: pointer; 
            border-radius: 8px; 
            position: fixed; 
            top: 15px; 
            left: 15px; 
            z-index: 1001; 
        }
        
        @media (max-width: 992px) {
            .sidebar { 
                width: 260px;
                transform: translateX(-100%);
            }
            .sidebar.active { 
                transform: translateX(0);
            }
            .main-container { 
                margin-left: 0; 
            }
            .menu-toggle { 
                display: block; 
            }
            /* REVISI 4: Menambah padding atas agar konten tidak tertutup tombol menu */
            .main-content {
                padding: 70px 24px 24px 24px;
            }
        }

        .brand {
    display: flex;
    align-items: center;
    gap: 10px; /* jarak antara logo dan teks */
    font-weight: 600;
    font-size: 18px;
    color: #222;
    background: none;
}

/* untuk logo gambar */
.brand-logo {
    height: 40px;       /* tinggi ideal logo */
    width: auto;
    max-width: 60px;    /* biar gak kebesaran */
    object-fit: contain;
    background: transparent;
    border: none;
}

/* untuk ikon fontawesome (kalau gak ada logo) */
.brand-icon {
    font-size: 32px;
    color: #28a745;
}

/* responsif di layar kecil */
@media (max-width: 768px) {
    .brand {
        font-size: 16px;
        gap: 8px;
    }
    .brand-logo {
        height: 30px;
        max-width: 50px;
    }
}


        /* SECTION STYLES */
        @yield('styles')

        
    </style>
    
</head>
<body>

    <button class="menu-toggle" onclick="toggleMenu()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="sidebar" id="mySidebar">
<div class="brand">
    @php
        $pengaturan = \App\Models\Pengaturan::first();
    @endphp

    @if(isset($pengaturan->logo) && $pengaturan->logo)
        <img src="{{ $pengaturan->logo }}" alt="Logo" class="brand-logo">
    @else
        <i class="fa-solid fa-hospital brand-icon"></i>
    @endif

    <span class="brand-name">{{ $pengaturan->nama_instansi ?? 'Antrian Ruang Coding' }}</span>
</div>

        
        <div class="nav-section">
            <div class="nav-label">Administrasi</div>
            <ul class="nav-list">
                <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="icon"><i class="fa-solid fa-chart-line"></i></span> Dashboard
                </a></li>
                <li><a href="{{ route('admin.pusat-kontrol.index') }}" class="{{ request()->routeIs('admin.pusat-kontrol.index') ? 'active' : '' }}">
                    <span class="icon"><i class="fa-solid fa-volume-high"></i></span> Loket Panggilan
                </a></li>
                <li><a href="{{ route('admin.loket.index') }}" class="{{ request()->routeIs('admin.loket.index') ? 'active' : '' }}">
                    <span class="icon"><i class="fa-solid fa-building"></i></span> Manajemen Loket
                </a></li>
                <li><a href="{{ route('admin.antrian.index') }}" class="{{ request()->routeIs('admin.antrian.index') ? 'active' : '' }}">
                    <span class="icon"><i class="fa-solid fa-list-ol"></i></span> Daftar Antrian
                </a></li>
                <li><a href="{{ route('admin.layanan.index') }}" class="{{ request()->routeIs('admin.layanan.index') ? 'active' : '' }}">
                    <span class="icon"><i class="fa-solid fa-server"></i></span> Manajemen Layanan
                </a></li>
            </ul>
        </div>
        
        <div class="nav-section">
            <div class="nav-label">Sistem & Pengguna</div>
            <ul class="nav-list">
                <li><a href="{{ route('admin.pengguna.index') }}" class="{{ request()->routeIs('admin.pengguna.index') ? 'active' : '' }}">
                    <span class="icon"><i class="fa-solid fa-users"></i></span> Manajemen Pengguna
                </a></li>
                <li><a href="{{ route('admin.pengaturan.index') }}" class="{{ request()->routeIs('admin.pengaturan.index') ? 'active' : '' }}">
                    <span class="icon"><i class="fa-solid fa-gear"></i></span> Pengaturan Sistem
                </a></li>
                <li><a href="{{ route('admin.advanced-settings.index') }}" class="{{ request()->routeIs('admin.advanced-settings.index') ? 'active' : '' }}">
                    <span class="icon"><i class="fa-solid fa-tv"></i></span> Display & Kiosk Settings
                </a></li>
            </ul>
        </div>

        <div class="nav-section">
            <div class="nav-label">Akun</div>
            <ul class="nav-list">
                <li>
                    <form action="{{ route('logout') }}" method="POST" style="width: 100%;">
                        @csrf
                        <button type="submit" class="logout-button">
                            <span class="icon"><i class="fa-solid fa-right-from-bracket"></i></span>
                            Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>

    <div class="main-container" id="mainContainer">
        
        <div class="main-content">
            @yield('content')
        </div>
        
    </div>

    <script>
        // Toggle Sidebar on Mobile
        function toggleMenu() {
            const sidebar = document.getElementById('mySidebar');
            const mainContainer = document.getElementById('mainContainer');
            sidebar.classList.toggle('active');
        }
        
        // Script Section
        @yield('scripts')
    </script>
</body>
</html>