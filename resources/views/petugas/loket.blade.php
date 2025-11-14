<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Loket Pelayanan - {{ $loket->nama_loket }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        /* CSS Variables untuk Theme Switching */
        :root {
            /* Light Theme Colors */
            --bg-primary: #f8fafc;
            --bg-secondary: #ffffff;
            --bg-card: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border-color: #e2e8f0;
            --accent-primary: #3b82f6;
            --accent-success: #10b981;
            --accent-danger: #ef4444;
            --accent-warning: #f59e0b;
            --accent-info: #3b82f6;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --transition-speed: 0.3s;
        }

        [data-theme="dark"] {
            /* Dark Theme Colors */
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-card: #1e293b;
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --text-muted: #64748b;
            --border-color: #334155;
            --accent-primary: #60a5fa;
            --accent-success: #34d399;
            --accent-danger: #f87171;
            --accent-warning: #fbbf24;
            --accent-info: #60a5fa;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.4), 0 2px 4px -1px rgba(0, 0, 0, 0.3);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.4), 0 4px 6px -2px rgba(0, 0, 0, 0.3);
        }

        /* Reset & Base */
        *, *::before, *::after {
            box-sizing: border-box;
        }
        
        html, body {
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden;
            transition: background-color var(--transition-speed) ease, color var(--transition-speed) ease;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            font-size: 14px;
        }

        /* Theme Toggle Button */
        .theme-toggle {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1000;
            transition: all var(--transition-speed) ease;
        }

        .theme-toggle:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.3);
        }

        .theme-toggle i {
            font-size: 24px;
            color: var(--accent-primary);
            transition: all var(--transition-speed) ease;
        }

        /* Container Utama (Full Screen Grid) */
        .petugas-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            height: 100vh;
            width: 100vw;
            gap: 0;
        }

        /* ================================= */
        /* === KOLOM KIRI (AKSI) === */
        /* ================================= */
        .kolom-kiri {
            display: flex;
            flex-direction: column;
            padding: 32px;
            gap: 24px;
            overflow-y: auto;
            background: var(--bg-primary);
            transition: background-color var(--transition-speed) ease;
        }

        /* Info Header */
        .header-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 20px 24px;
            box-shadow: var(--shadow-sm);
            transition: all var(--transition-speed) ease;
        }
        .header-card:hover {
            box-shadow: var(--shadow-md);
        }
        .header-info h1 {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-primary);
            margin: 0 0 4px 0;
        }
        .header-info p {
            font-size: 15px;
            color: var(--text-secondary);
            margin: 0;
            font-weight: 500;
        }
        .header-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }
        
        /* Tombol Buka/Tutup & Logout (Gaya Modern) */
        .btn-header {
            padding: 10px 16px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-toggle-loket.btn-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--accent-success);
        }
        .btn-toggle-loket.btn-success:hover { 
            background-color: rgba(16, 185, 129, 0.2);
            transform: translateY(-2px);
        }
        .btn-toggle-loket.btn-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--accent-danger);
        }
        .btn-toggle-loket.btn-danger:hover { 
            background-color: rgba(239, 68, 68, 0.2);
            transform: translateY(-2px);
        }
        
        .btn-logout {
            background: none;
            color: var(--text-secondary);
            font-size: 20px;
            padding: 8px;
        }
        .btn-logout:hover {
            color: var(--accent-danger);
            background-color: rgba(239, 68, 68, 0.1);
        }

        /* Kartu Antrian Saat Ini */
        .current-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 32px;
            text-align: center;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            transition: all var(--transition-speed) ease;
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
        }

        .current-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-primary), var(--accent-success));
            opacity: 0;
            transition: opacity var(--transition-speed) ease;
        }

        .current-card:hover::before {
            opacity: 1;
        }

        .current-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .current-card p {
            font-size: 18px;
            font-weight: 500;
            color: var(--text-secondary);
            margin: 0 0 10px 0;
        }
        .current-card .nomor-display {
            font-size: 120px;
            font-weight: 900;
            margin: 10px 0;
            line-height: 1;
            transition: all var(--transition-speed) ease;
        }
        .current-card .status-display {
            font-size: 20px;
            font-weight: 700;
            padding: 8px 20px;
            border-radius: 12px;
            margin-top: 10px;
            transition: all var(--transition-speed) ease;
        }

        /* Status Warna Kartu Antrian */
        .current-card.status-idle {
            background: var(--bg-primary);
            border-style: dashed;
        }
        .current-card.status-idle .nomor-display { color: var(--text-muted); }
        .current-card.status-idle .status-display { 
            background: var(--border-color); 
            color: var(--text-secondary); 
        }
        
        .current-card.status-dipanggil { 
            border-color: var(--accent-primary);
            background: linear-gradient(135deg, var(--bg-card), rgba(59, 130, 246, 0.05));
        }
        .current-card.status-dipanggil .nomor-display { 
            color: var(--accent-primary);
            text-shadow: 0 0 10px rgba(59, 130, 246, 0.3);
        }
        .current-card.status-dipanggil .status-display { 
            background: rgba(59, 130, 246, 0.1); 
            color: var(--accent-primary); 
        }
        
        .current-card.status-dilayani { 
            border-color: var(--accent-success);
            background: linear-gradient(135deg, var(--bg-card), rgba(16, 185, 129, 0.05));
        }
        .current-card.status-dilayani .nomor-display { 
            color: var(--accent-success);
            text-shadow: 0 0 10px rgba(16, 185, 129, 0.3);
        }
        .current-card.status-dilayani .status-display { 
            background: rgba(16, 185, 129, 0.1); 
            color: var(--accent-success); 
        }

        .current-card.status-tutup {
            background: var(--bg-primary);
            border-color: var(--border-color);
            border-style: dashed;
        }
        .current-card.status-tutup .nomor-display { 
            color: var(--text-muted); 
            font-size: 90px; 
        }
        .current-card.status-tutup .status-display { 
            background: var(--border-color); 
            color: var(--text-secondary); 
        }

        /* Tombol Aksi */
        .action-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }
        .btn-aksi {
            padding: 20px;
            font-size: 18px;
            font-weight: 700;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }
        
        .btn-aksi::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(255,255,255,0.1), rgba(255,255,255,0.3));
            transform: translateX(-100%);
            transition: transform 0.6s;
        }
        
        .btn-aksi:hover::before {
            transform: translateX(100%);
        }

        /* Style untuk tombol yang dinonaktifkan */
        .btn-aksi:disabled {
            background: linear-gradient(145deg, var(--bg-primary), var(--border-color));
            color: var(--text-muted);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
            opacity: 0.7;
            position: relative;
            overflow: hidden;
        }

        /* Tambahkan efek animasi untuk tombol yang dinonaktifkan */
        .btn-aksi:disabled::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        /* Tambahkan efek tooltip untuk tombol yang dinonaktifkan */
        .btn-aksi:disabled::after {
            content: attr(data-disabled-reason);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background-color: var(--text-primary);
            color: var(--bg-primary);
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
            margin-bottom: 8px;
            z-index: 10;
        }

        .btn-aksi:disabled:hover::after {
            opacity: 1;
        }

        /* Warna Tombol Aksi */
        #btn-panggil { 
            background: linear-gradient(135deg, var(--accent-success), #059669); 
            color: white; 
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }
        #btn-panggil:hover:not(:disabled) { 
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }
        
        #btn-layani { 
            background: linear-gradient(135deg, var(--accent-primary), #2563eb); 
            color: white; 
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }
        #btn-layani:hover:not(:disabled) { 
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        }
        
        .sub-actions { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 16px; 
        }
        
        #btn-selesai { 
            background: linear-gradient(135deg, #8b5cf6, #7c3aed); 
            color: white; 
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        }
        #btn-selesai:hover:not(:disabled) { 
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.4);
        }
        
        #btn-batalkan { 
            background: linear-gradient(135deg, var(--accent-danger), #dc2626); 
            color: white; 
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        }
        #btn-batalkan:hover:not(:disabled) { 
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
        }

        /* ================================= */
        /* === KOLOM KANAN (INFO) === */
        /* ================================= */
        .kolom-kanan {
            display: flex;
            flex-direction: column;
            height: 100vh;
            background: var(--bg-secondary);
            border-left: 1px solid var(--border-color);
            overflow: hidden;
            transition: all var(--transition-speed) ease;
        }
        .list-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .list-wrapper:first-child {
            border-bottom: 1px solid var(--border-color);
        }
        .list-header {
            padding: 16px 24px;
            border-bottom: 1px solid var(--border-color);
            flex-shrink: 0;
            background: var(--bg-card);
        }
        .list-header h3 {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .list-header .badge {
            background: var(--border-color);
            color: var(--text-secondary);
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 10px;
            font-weight: 600;
        }
        
        /* Ini adalah list yang bisa scroll */
        .list-body {
            overflow-y: auto;
            flex-grow: 1;
            padding: 8px;
            scrollbar-width: thin;
            scrollbar-color: var(--border-color) transparent;
        }
        
        /* Custom Scrollbar */
        .list-body::-webkit-scrollbar {
            width: 6px;
        }
        
        .list-body::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .list-body::-webkit-scrollbar-thumb {
            background-color: var(--border-color);
            border-radius: 3px;
        }
        
        .list-body::-webkit-scrollbar-thumb:hover {
            background-color: var(--text-muted);
        }
        
        .list-body ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .list-body li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            border-radius: 8px;
            transition: all 0.2s;
            margin-bottom: 4px;
        }
        .list-body li:hover {
            background-color: var(--bg-primary);
            transform: translateX(4px);
        }
        .list-body li strong {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-primary);
        }
        .list-body li span {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
        }
        .list-body .empty-list {
            text-align: center;
            padding: 40px;
            color: var(--text-muted);
            font-size: 14px;
        }
        
        /* Style untuk Riwayat */
        #list-riwayat li strong {
            font-weight: 500;
        }
        #list-riwayat li .status-selesai {
            color: var(--accent-success);
            font-weight: 600;
        }
        #list-riwayat li .status-batal {
            color: var(--accent-danger);
            font-weight: 600;
        }

        /* Custom Toast Styles */
        .toastify {
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-weight: 500;
            box-shadow: var(--shadow-lg);
            padding: 16px 20px !important;
            min-height: 50px;
            display: flex;
            align-items: center;
            color: white;
            font-size: 15px;
            line-height: 1.5;
            backdrop-filter: blur(10px);
        }
        
        .toastify.toast-success {
            background: linear-gradient(135deg, var(--accent-success), #059669) !important;
            color: white;
        }
        
        .toastify.toast-error {
            background: linear-gradient(135deg, var(--accent-danger), #dc2626) !important;
            color: white;
        }
        
        .toastify.toast-warning {
            background: linear-gradient(135deg, var(--accent-warning), #d97706) !important;
            color: white;
        }
        
        .toastify.toast-info {
            background: linear-gradient(135deg, var(--accent-info), #2563eb) !important;
            color: white;
        }
        
        /* Close button styling */
        .toastify button {
            background: rgba(255, 255, 255, 0.3) !important;
            color: white !important;
            border-radius: 4px !important;
            padding: 4px 8px !important;
            font-weight: 600 !important;
        }
        
        .toastify button:hover {
            background: rgba(255, 255, 255, 0.5) !important;
        }

        /* ================================= */
        /* === CONFIRM DIALOG MODAL === */
        /* ================================= */
        .confirm-dialog-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            animation: fadeIn 0.3s ease;
        }

        .confirm-dialog-overlay.show {
            display: flex;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .confirm-dialog-box {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 32px;
            max-width: 450px;
            width: 90%;
            box-shadow: var(--shadow-lg);
            animation: slideUp 0.3s ease;
            text-align: center;
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .confirm-dialog-icon {
            font-size: 48px;
            margin-bottom: 20px;
            display: block;
            animation: bounceIn 0.5s ease;
        }

        @keyframes bounceIn {
            0% {
                transform: scale(0.8);
                opacity: 0;
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .confirm-dialog-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0 0 12px 0;
        }

        .confirm-dialog-message {
            font-size: 16px;
            color: var(--text-secondary);
            margin-bottom: 32px;
            line-height: 1.6;
        }

        .confirm-dialog-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .confirm-dialog-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-width: 120px;
        }

        .confirm-dialog-btn:hover {
            transform: translateY(-2px);
        }

        .confirm-dialog-btn:active {
            transform: translateY(0);
        }

        .confirm-dialog-btn.btn-yes {
            background: linear-gradient(135deg, var(--accent-warning), #d97706);
            color: white;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }

        .confirm-dialog-btn.btn-yes:hover {
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
        }

        .confirm-dialog-btn.btn-no {
            background: var(--border-color);
            color: var(--text-primary);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .confirm-dialog-btn.btn-no:hover {
            background: rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .petugas-container {
                grid-template-columns: 1fr;
                height: auto;
                overflow-y: auto;
            }
            .kolom-kanan {
                height: 600px;
            }
            .current-card .nomor-display {
                font-size: 90px;
            }
            .theme-toggle {
                bottom: 16px;
                right: 16px;
                width: 48px;
                height: 48px;
            }
            .theme-toggle i {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>

    <div class="petugas-container">
        
        <div class="kolom-kiri">
            
            <div class="header-card">
                <div class="header-info">
                    <h1>{{ $loket->nama_loket }}</h1>
                    <p>{{ $loket->layanan->nama_layanan }}</p>
                </div>
                <div class="header-actions">
                    <button class="btn-header btn-toggle-loket" id="btn-toggle-loket" onclick="toggleLoket()">
                        <i class="fas fa-power-off"></i>
                        <span id="toggle-loket-text">...</span>
                    </button>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-header btn-logout" title="Logout">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="current-card status-idle" id="current-antrian-card">
                <p>Antrian Saat Ini</p>
                <h2 class="nomor-display" id="current-kode">-</h2>
                <span class="status-display" id="current-status">Tidak Ada Antrian</span>
            </div>

            <div class="action-grid">
                <button class="btn-aksi" id="btn-panggil" onclick="panggil()" data-disabled-reason="Tidak dapat memanggil saat ini">
                    <i class="fas fa-bullhorn"></i> PANGGIL BERIKUTNYA
                </button>
                <button class="btn-aksi" id="btn-layani" onclick="layani()" data-disabled-reason="Tidak ada antrian untuk dilayani">
                    <i class="fas fa-play-circle"></i> MULAI LAYANI
                </button>
                <div class="sub-actions">
                    <button class="btn-aksi" id="btn-selesai" onclick="selesai()" data-disabled-reason="Tidak ada antrian yang sedang dilayani">
                        <i class="fas fa-check-circle"></i> SELESAI
                    </button>
                    <button class="btn-aksi" id="btn-batalkan" onclick="batalkan()" data-disabled-reason="Tidak ada antrian untuk dibatalkan">
                        <i class="fas fa-times-circle"></i> BATALKAN
                    </button>
                </div>
            </div>

        </div>

        <div class="kolom-kanan">
            
            <div class="list-wrapper">
                <div class="list-header">
                    <h3>
                        Daftar Menunggu
                        <span class="badge" id="count-menunggu">0</span>
                    </h3>
                </div>
                <div class="list-body">
                    <ul id="list-menunggu">
                        <li class="empty-list">Memuat antrian...</li>
                    </ul>
                </div>
            </div>
            
            <div class="list-wrapper">
                <div class="list-header">
                    <h3>
                        Riwayat Hari Ini
                        <span class="badge" id="count-selesai">0</span>
                    </h3>
                </div>
                <div class="list-body">
                    <ul id="list-riwayat">
                        <li class="empty-list">Memuat riwayat...</li>
                    </ul>
                </div>
            </div>

        </div>

    </div>

    <!-- Theme Toggle Button -->
    <div class="theme-toggle" id="theme-toggle" title="Toggle Dark Mode">
        <i class="fas fa-moon" id="theme-icon"></i>
    </div>

    <!-- Confirm Dialog Modal -->
    <div class="confirm-dialog-overlay" id="confirm-dialog-overlay">
        <div class="confirm-dialog-box">
            <i class="fas fa-exclamation-triangle confirm-dialog-icon" id="confirm-dialog-icon" style="color: var(--accent-warning);"></i>
            <h2 class="confirm-dialog-title" id="confirm-dialog-title">Konfirmasi</h2>
            <p class="confirm-dialog-message" id="confirm-dialog-message">Apakah Anda yakin?</p>
            <div class="confirm-dialog-actions">
                <button class="confirm-dialog-btn btn-no" onclick="confirmDialogNo()">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button class="confirm-dialog-btn btn-yes" id="confirm-dialog-yes-btn" onclick="confirmDialogYes()">
                    <i class="fas fa-check"></i> Ya, Lanjutkan
                </button>
            </div>
        </div>
    </div>
    
    @vite(['resources/js/bootstrap.js'])

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    
    <script>
        // Theme Toggle Functionality
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const htmlElement = document.documentElement;
        
        // Check for saved theme preference or default to light
        const currentTheme = localStorage.getItem('theme') || 'light';
        htmlElement.setAttribute('data-theme', currentTheme);
        updateThemeIcon(currentTheme);
        
        themeToggle.addEventListener('click', () => {
            const theme = htmlElement.getAttribute('data-theme');
            const newTheme = theme === 'light' ? 'dark' : 'light';
            
            htmlElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeIcon(newTheme);
            
            // Show toast notification
            showToast(`Mode ${newTheme === 'dark' ? 'Gelap' : 'Terang'} diaktifkan`, 'info', 2000);
        });
        
        function updateThemeIcon(theme) {
            if (theme === 'dark') {
                themeIcon.classList.remove('fa-moon');
                themeIcon.classList.add('fa-sun');
            } else {
                themeIcon.classList.remove('fa-sun');
                themeIcon.classList.add('fa-moon');
            }
        }

        // URLs
        const URL_GET_LIST = "{{ route('petugas.loket.list') }}";
        const URL_PANGGIL = "{{ route('petugas.loket.panggil') }}";
        const URL_LAYANI = "{{ route('petugas.loket.layani') }}";
        const URL_SELESAI = "{{ route('petugas.loket.selesai') }}";
        const URL_BATALKAN = "{{ route('petugas.loket.batalkan') }}";
        const URL_TOGGLE_LOKET = "{{ route('petugas.loket.tutup') }}";
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Elemen Tombol
        const btnPanggil = document.getElementById('btn-panggil');
        const btnLayani = document.getElementById('btn-layani');
        const btnSelesai = document.getElementById('btn-selesai');
        const btnBatalkan = document.getElementById('btn-batalkan');
        const btnToggleLoket = document.getElementById('btn-toggle-loket');
        const toggleLoketText = document.getElementById('toggle-loket-text');

        // Elemen Data
        const currentCard = document.getElementById('current-antrian-card');
        const currentKode = document.getElementById('current-kode');
        const currentStatus = document.getElementById('current-status');
        const listMenunggu = document.getElementById('list-menunggu');
        const listRiwayat = document.getElementById('list-riwayat');
        const countMenunggu = document.getElementById('count-menunggu');
        const countSelesai = document.getElementById('count-selesai');

        // Variabel Status
        let currentAntrian = null;
        let loketStatus = '{{ $loket->status }}';

        // Variabel untuk confirm dialog
        let confirmDialogCallback = null;
        let confirmDialogOverlay = document.getElementById('confirm-dialog-overlay');
        let confirmDialogTitle = document.getElementById('confirm-dialog-title');
        let confirmDialogMessage = document.getElementById('confirm-dialog-message');
        let confirmDialogIcon = document.getElementById('confirm-dialog-icon');
        let confirmDialogYesBtn = document.getElementById('confirm-dialog-yes-btn');

        /**
         * Tampilkan Confirm Dialog
         */
        function showConfirmDialog(title, message, onConfirm, iconClass = 'fa-exclamation-triangle', iconColor = 'var(--accent-warning)') {
            confirmDialogTitle.textContent = title;
            confirmDialogMessage.textContent = message;
            confirmDialogIcon.className = `fas ${iconClass} confirm-dialog-icon`;
            confirmDialogIcon.style.color = iconColor;
            confirmDialogCallback = onConfirm;
            confirmDialogOverlay.classList.add('show');
        }

        /**
         * Confirm Dialog - Yes Button
         */
        function confirmDialogYes() {
            if (confirmDialogCallback) {
                confirmDialogCallback();
            }
            closeConfirmDialog();
        }

        /**
         * Confirm Dialog - No Button
         */
        function confirmDialogNo() {
            closeConfirmDialog();
        }

        /**
         * Close Confirm Dialog
         */
        function closeConfirmDialog() {
            confirmDialogOverlay.classList.remove('show');
            confirmDialogCallback = null;
        }

        /**
         * Close dialog jika klik di luar
         */
        confirmDialogOverlay.addEventListener('click', (e) => {
            if (e.target === confirmDialogOverlay) {
                closeConfirmDialog();
            }
        });

        /**
         * Close dialog jika tekan Escape
         */
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && confirmDialogOverlay.classList.contains('show')) {
                closeConfirmDialog();
            }
        });

        /**
         * Fungsi untuk menampilkan toast notification
         */
        function showToast(message, type = 'info', duration = 3000) {
            const toastClass = `toast-${type}`;
            
            Toastify({
                text: message,
                duration: duration,
                close: true,
                gravity: "top",
                position: "right",
                stopOnFocus: true,
                className: toastClass,
                style: {
                    background: "transparent",
                    boxShadow: "none",
                    padding: "0"
                },
                onClick: function(){} 
            }).showToast();
        }

        /**
         * Fungsi utama untuk mengambil data terbaru
         */
        async function fetchAntrianData() {
            try {
                const response = await fetch(URL_GET_LIST);
                if (!response.ok) throw new Error('Network response was not ok');
                
                const data = await response.json();
                
                if (data.error) {
                    console.error('[ANTRIAN ERROR]', data.error);
                    updateCurrentCard(null);
                    updateWaitingList([], 0);
                    updateHistoryList([], 0);
                    return;
                }
                
                currentAntrian = data.current; 
                loketStatus = data.loket_status; 
                
                updateCurrentCard(data.current);
                updateWaitingList(data.waiting || [], data.stats?.menunggu_total || 0);
                updateHistoryList(data.history || [], data.stats?.selesai_total || 0); // Menggunakan stats.selesai_total
                updateButtonState();
                updateLoketStatusUI();
                
                console.log('[ANTRIAN OK] Waiting:', data.waiting?.length || 0, 'History:', data.history?.length || 0);

            } catch (error) {
                console.error("[ANTRIAN ERROR] Gagal mengambil data antrian:", error);
                // Non-aktifkan tombol jika fetch gagal
                btnPanggil.disabled = true;
                btnLayani.disabled = true;
                btnSelesai.disabled = true;
                btnBatalkan.disabled = true;
                currentStatus.textContent = "Error Koneksi";
                showToast("Gagal mengambil data antrian. Periksa koneksi internet Anda.", "error");
            }
        }

        /**
         * Update Kartu Antrian Saat Ini
         */
        function updateCurrentCard(antrian) {
            // Hapus semua status kelas
            currentCard.classList.remove('status-idle', 'status-dipanggil', 'status-dilayani', 'status-tutup');

            if (loketStatus === 'tutup') {
                currentCard.classList.add('status-tutup');
                currentKode.textContent = "LOKET";
                currentStatus.textContent = "TUTUP";
                return;
            }

            if (antrian) {
                currentKode.textContent = antrian.kode_antrian;
                currentStatus.textContent = antrian.status.toUpperCase();
                
                if (antrian.status === 'dipanggil') {
                    currentCard.classList.add('status-dipanggil');
                } else if (antrian.status === 'dilayani') {
                    currentCard.classList.add('status-dilayani');
                } else {
                    currentCard.classList.add('status-idle'); // Fallback
                }
            } else {
                currentCard.classList.add('status-idle');
                currentKode.textContent = "-";
                currentStatus.textContent = "Tidak Ada Antrian";
            }
        }

        /**
         * Update Daftar Menunggu
         */
        function updateWaitingList(waiting, total) {
            countMenunggu.textContent = total;
            if (waiting.length === 0) {
                listMenunggu.innerHTML = '<li class="empty-list">Tidak ada antrian menunggu.</li>';
                return;
            }
            let html = '';
            waiting.forEach(a => {
                html += `<li>
                            <strong>${a.kode_antrian}</strong>
                            <span>${new Date(a.waktu_ambil).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' })}</span>
                         </li>`;
            });
            listMenunggu.innerHTML = html;
        }

        /**
         * Update Daftar Riwayat
         */
        function updateHistoryList(history, total) {
            countSelesai.textContent = total;
            if (history.length === 0) {
                listRiwayat.innerHTML = '<li class="empty-list">Belum ada riwayat.</li>';
                return;
            }
            let html = '';
            history.forEach(a => {
                const statusClass = a.status === 'batal' ? 'status-batal' : 'status-selesai';
                const time = a.waktu_selesai || a.waktu_panggil || a.waktu_ambil; // Fallback waktu
                html += `<li>
                            <div>
                                <strong>${a.kode_antrian}</strong>
                                <span style="display: block;" class="${statusClass}">${a.status.toUpperCase()}</span>
                            </div>
                            <span>${new Date(time).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}</span>
                         </li>`;
            });
            listRiwayat.innerHTML = html;
        }

        /**
         * Mengatur tombol mana yang bisa diklik
         */
        function updateButtonState() {
            if (loketStatus === 'tutup') {
                btnPanggil.disabled = true;
                btnLayani.disabled = true;
                btnSelesai.disabled = true;
                btnBatalkan.disabled = true;
                
                // Update alasan disabled
                btnPanggil.setAttribute('data-disabled-reason', 'Loket sedang ditutup');
                btnLayani.setAttribute('data-disabled-reason', 'Loket sedang ditutup');
                btnSelesai.setAttribute('data-disabled-reason', 'Loket sedang ditutup');
                btnBatalkan.setAttribute('data-disabled-reason', 'Loket sedang ditutup');
                return;
            }

            if (currentAntrian) {
                if (currentAntrian.status === 'dipanggil') {
                    btnPanggil.disabled = true;
                    btnLayani.disabled = false;
                    btnSelesai.disabled = false;
                    btnBatalkan.disabled = false;
                    
                    // Update alasan disabled
                    btnPanggil.setAttribute('data-disabled-reason', 'Sudah memanggil antrian');
                } else if (currentAntrian.status === 'dilayani') {
                    btnPanggil.disabled = true;
                    btnLayani.disabled = true;
                    btnSelesai.disabled = false;
                    btnBatalkan.disabled = false;
                    
                    // Update alasan disabled
                    btnPanggil.setAttribute('data-disabled-reason', 'Sedang melayani antrian');
                    btnLayani.setAttribute('data-disabled-reason', 'Sedang melayani antrian');
                }
            } else {
                btnPanggil.disabled = false; // Bisa memanggil jika tidak ada antrian
                btnLayani.disabled = true;
                btnSelesai.disabled = true;
                btnBatalkan.disabled = true;
                
                // Update alasan disabled
                btnLayani.setAttribute('data-disabled-reason', 'Tidak ada antrian untuk dilayani');
                btnSelesai.setAttribute('data-disabled-reason', 'Tidak ada antrian yang sedang dilayani');
                btnBatalkan.setAttribute('data-disabled-reason', 'Tidak ada antrian untuk dibatalkan');
            }
        }
        
        /**
         * Mengatur UI tombol Buka/Tutup Loket
         */
        function updateLoketStatusUI() {
            if (loketStatus === 'aktif') {
                btnToggleLoket.classList.remove('btn-success');
                btnToggleLoket.classList.add('btn-danger');
                toggleLoketText.textContent = 'Tutup Loket';
            } else {
                btnToggleLoket.classList.remove('btn-danger');
                btnToggleLoket.classList.add('btn-success');
                toggleLoketText.textContent = 'Buka Loket';
            }
        }

        /**
         * Fungsi POST universal untuk tombol
         */
        async function postAksi(url, aksi, successMessage = null) {
            // Nonaktifkan semua tombol sementara
            btnPanggil.disabled = true;
            btnLayani.disabled = true;
            btnSelesai.disabled = true;
            btnBatalkan.disabled = true;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                });
                const data = await response.json();
                if (!data.success) {
                    showToast(data.message || `Gagal ${aksi}`, "error");
                } else if (successMessage) {
                    showToast(successMessage, "success");
                }
                fetchAntrianData(); // Refresh data setelah aksi
            } catch (error) {
                console.error(`Gagal ${aksi}:`, error);
                showToast(`Gagal ${aksi}. Periksa konsol.`, "error");
                fetchAntrianData(); // Refresh data
            }
        }

        // Fungsi Tombol
        function panggil() { 
            postAksi(URL_PANGGIL, 'memanggil', 'Berhasil memanggil antrian berikutnya'); 
        }
        function layani() { 
            postAksi(URL_LAYANI, 'melayani', 'Berhasil mulai melayani antrian'); 
        }
        function selesai() { 
            postAksi(URL_SELESAI, 'menyelesaikan', 'Antrian telah selesai dilayani'); 
        }
        function batalkan() { 
            showConfirmDialog(
                'Batalkan Antrian',
                'Apakah Anda yakin ingin membatalkan antrian ini? Tindakan ini tidak dapat dibatalkan.',
                () => {
                    postAksi(URL_BATALKAN, 'membatalkan', 'Antrian berhasil dibatalkan');
                },
                'fa-times-circle',
                'var(--accent-danger)'
            );
        }
        async function toggleLoket() {
            const aksi = (loketStatus === 'aktif') ? 'menutup' : 'membuka';
            const message = (loketStatus === 'aktif') 
                ? 'Loket berhasil ditutup' 
                : 'Loket berhasil dibuka';
            
            const title = loketStatus === 'aktif' ? 'Tutup Loket' : 'Buka Loket';
            const confirmMsg = loketStatus === 'aktif' 
                ? 'Apakah Anda yakin ingin menutup loket? Antrian menunggu akan tetap tersimpan.'
                : 'Apakah Anda yakin ingin membuka loket? Petugas siap melayani.';
            const icon = loketStatus === 'aktif' ? 'fa-power-off' : 'fa-unlock';
            const color = loketStatus === 'aktif' ? 'var(--accent-danger)' : 'var(--accent-success)';
                
            showConfirmDialog(
                title,
                confirmMsg,
                async () => {
                    try {
                        const response = await fetch(URL_TOGGLE_LOKET, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': CSRF_TOKEN
                            },
                        });
                        const data = await response.json();
                        if (data.success) {
                            loketStatus = data.new_status;
                            updateLoketStatusUI();
                            updateButtonState();
                            updateCurrentCard(currentAntrian);
                            showToast(message, "success");
                        } else {
                            showToast(data.message || `Gagal ${aksi} loket`, "error");
                        }
                    } catch (error) {
                        console.error('Gagal toggle loket:', error);
                        showToast(`Gagal ${aksi} loket. Periksa konsol.`, "error");
                    }
                },
                icon,
                color
            );
        }

        /**
         * Tampilkan Pesan dari Admin di Tengah Layar
         */
        function showAdminMessage(pesan, adminName, messageType = 'info') {
            // Buat container modal jika belum ada
            let modal = document.getElementById('admin-message-modal');
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'admin-message-modal';
                modal.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.5);
                    display: none;
                    align-items: center;
                    justify-content: center;
                    z-index: 10000;
                `;
                document.body.appendChild(modal);
            }

            // Tentukan warna berdasarkan tipe pesan
            let bgColor = getComputedStyle(document.documentElement).getPropertyValue('--accent-info'); // default: info
            let iconClass = 'fa-info-circle';
            
            if (messageType === 'warning') {
                bgColor = getComputedStyle(document.documentElement).getPropertyValue('--accent-warning');
                iconClass = 'fa-exclamation-triangle';
            } else if (messageType === 'error') {
                bgColor = getComputedStyle(document.documentElement).getPropertyValue('--accent-danger');
                iconClass = 'fa-exclamation-circle';
            } else if (messageType === 'success') {
                bgColor = getComputedStyle(document.documentElement).getPropertyValue('--accent-success');
                iconClass = 'fa-check-circle';
            }

            // Buat konten pesan
            const messageBox = document.createElement('div');
            messageBox.style.cssText = `
                background: ${bgColor};
                color: white;
                padding: 40px;
                border-radius: 10px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
                max-width: 500px;
                text-align: center;
                font-size: 18px;
                line-height: 1.6;
            `;
            
            messageBox.innerHTML = `
                <i class="fa-solid ${iconClass}" style="font-size: 48px; margin-bottom: 20px; display: block;"></i>
                <div style="margin-bottom: 20px; font-weight: bold;">${pesan}</div>
                <div style="font-size: 14px; opacity: 0.9;">Dari: ${adminName}</div>
            `;

            modal.innerHTML = '';
            modal.appendChild(messageBox);
            modal.style.display = 'flex';

            // Auto-hide setelah 5 detik
            setTimeout(() => {
                modal.style.display = 'none';
            }, 5000);

            // Klik di luar untuk menutup
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }

        // ============================================================
        // INISIALISASI
        // ============================================================
        document.addEventListener('DOMContentLoaded', () => {
            console.log('Halaman Loket Petugas Dimuat.');
            
            fetchAntrianData();
            
            setInterval(fetchAntrianData, 5000); // Polling fallback

            if (typeof Echo !== 'undefined') {
                console.log('Echo terdeteksi, mencoba terhubung ke channel...');
                Echo.channel('antrian') // Nama channel publik
                    .listen('.antrian.dipanggil', (e) => {
                        console.log('Event [antrian.dipanggil] diterima:', e);
                        // Cek apakah event ini untuk layanan yang dilayani loket ini
                        if (e.layanan_id === {{ $loket->layanan_id }}) {
                            fetchAntrianData();
                        }
                    })
                    .listen('.loket.status.updated', (e) => {
                        console.log('Event [loket.status.updated] diterima:', e);
                        // Cek apakah event ini untuk loket ini
                        if (e.loket_id === {{ $loket->id }}) {
                            fetchAntrianData();
                        }
                    })
                    .error((error) => {
                        console.error('Echo connection error:', error);
                    });
                
                // Listen to private messages for this loket
                Echo.private('loket-{{ $loket->id }}')
                    .listen('.admin.message.sent', (e) => {
                        console.log('Pesan dari admin diterima:', e);
                        showAdminMessage(e.pesan, e.admin_name, e.message_type || 'info');
                    })
                    .error((error) => {
                        console.error('Error on private channel:', error);
                    });
                
                Echo.connector.pusher.connection.bind('connected', () => {
                    console.log('Echo BERHASIL terhubung (Real-time Aktif).');
                });
                Echo.connector.pusher.connection.bind('disconnected', () => {
                    console.warn('Echo TERPUTUS (Mengandalkan polling 5 detik).');
                });

            } else {
                console.warn('Echo (Pusher) tidak ditemukan. Halaman akan menggunakan polling 5 detik.');
            }
        });

    </script>
</body>
</html>