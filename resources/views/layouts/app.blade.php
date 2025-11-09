<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Antrian')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif; background: #f5f5f5; color: #333; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .navbar { background: #2c3e50; color: white; padding: 15px 20px; position: sticky; top: 0; z-index: 100; box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
        .navbar-content { max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .navbar h1 { font-size: 20px; font-weight: 600; }
        .navbar a { color: white; text-decoration: none; margin-left: 20px; transition: opacity 0.2s; }
        .navbar a:hover { opacity: 0.8; }
        .navbar-right { display: flex; align-items: center; gap: 15px; }
        .navbar-user { background: rgba(255,255,255,0.1); padding: 8px 15px; border-radius: 5px; }
        .sidebar { width: 260px; background: white; min-height: calc(100vh - 60px); position: fixed; left: 0; top: 60px; padding: 20px 0; box-shadow: 2px 0 8px rgba(0,0,0,0.1); overflow-y: auto; }
        .sidebar a { display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: #555; text-decoration: none; margin: 2px 10px; border-radius: 5px; transition: all 0.2s; }
        .sidebar a:hover { background: #e8f4f8; color: #3498db; }
        .sidebar a.active { background: #3498db; color: white; }
        .sidebar hr { margin: 15px 0; opacity: 0.2; }
        .main-content { margin-left: 280px; padding: 20px; }
        .card { background: white; padding: 25px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: box-shadow 0.2s; }
        .card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.12); }
        .card h2, .card h3 { margin-bottom: 15px; color: #2c3e50; }
        .btn { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-weight: 500; transition: all 0.2s; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .btn-primary { background: #3498db; color: white; }
        .btn-primary:hover { background: #2980b9; }
        .btn-success { background: #27ae60; color: white; }
        .btn-success:hover { background: #229954; }
        .btn-danger { background: #e74c3c; color: white; }
        .btn-danger:hover { background: #c0392b; }
        .btn-warning { background: #f39c12; color: white; }
        .btn-warning:hover { background: #d68910; }
        .btn-secondary { background: #95a5a6; color: white; }
        .btn-secondary:hover { background: #7f8c8d; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .btn-lg { padding: 15px 30px; font-size: 16px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table th, table td { padding: 12px; text-align: left; }
        table th { background: #f8f9fa; font-weight: 600; border-bottom: 2px solid #e8e8e8; }
        table td { border-bottom: 1px solid #ecf0f1; }
        table tr:hover { background: #f8f9fa; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 6px; font-weight: 500; color: #2c3e50; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; transition: border-color 0.2s; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #3498db; box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1); }
        .alert { padding: 15px 20px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid; }
        .alert-success { background: #d4edda; color: #155724; border-color: #27ae60; }
        .alert-danger { background: #f8d7da; color: #721c24; border-color: #e74c3c; }
        .alert-warning { background: #fff3cd; color: #856404; border-color: #f39c12; }
        .alert-info { background: #d1ecf1; color: #0c5460; border-color: #3498db; }
        .badge { display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-danger { background: #f8d7da; color: #721c24; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
        .badge-primary { background: #cfe2ff; color: #084298; }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); transition: transform 0.3s; }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .stats-grid { grid-template-columns: 1fr; }
        }
        @yield('styles')
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-content">
            <h1><i class="fas fa-hospital"></i> Sistem Antrian Digital</h1>
            @auth
            <div class="navbar-right">
                <div class="navbar-user">
                    <i class="fas fa-user-circle"></i> {{ Auth::user()->name }}
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-secondary btn-sm">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
            @endauth
        </div>
    </div>

    @auth
    @if(Auth::user()->role === 'admin')
    <div class="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-line"></i> Dashboard
        </a>
        <a href="{{ route('admin.layanan.index') }}" class="{{ request()->routeIs('admin.layanan.*') ? 'active' : '' }}">
            <i class="fas fa-list-ul"></i> Manajemen Layanan
        </a>
        <a href="{{ route('admin.loket.index') }}" class="{{ request()->routeIs('admin.loket.*') ? 'active' : '' }}">
            <i class="fas fa-door-open"></i> Manajemen Loket
        </a>
        <a href="{{ route('admin.antrian.index') }}" class="{{ request()->routeIs('admin.antrian.*') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list"></i> Daftar Antrian
        </a>
        <a href="{{ route('admin.analytics.index') }}" class="{{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}">
            <i class="fas fa-chart-bar"></i> Analytics & Reports
        </a>
        <a href="{{ route('admin.print.index') }}" class="{{ request()->routeIs('admin.print.*') ? 'active' : '' }}">
            <i class="fas fa-print"></i> Print Ulang Struk
        </a>
        <a href="{{ route('admin.pengguna.index') }}" class="{{ request()->routeIs('admin.pengguna.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i> Manajemen Pengguna
        </a>
        <a href="{{ route('admin.pengaturan.index') }}" class="{{ request()->routeIs('admin.pengaturan.*') ? 'active' : '' }}">
            <i class="fas fa-cog"></i> Pengaturan
        </a>
        <a href="{{ route('admin.advanced-settings.index') }}" class="{{ request()->routeIs('admin.advanced-settings.*') ? 'active' : '' }}">
            <i class="fas fa-sliders-h"></i> Pengaturan Lanjutan
        </a>
        <a href="{{ route('admin.audio-settings.index') }}" class="{{ request()->routeIs('admin.audio-settings.*') ? 'active' : '' }}">
            <i class="fas fa-volume-up"></i> Pengaturan Audio
        </a>
        <hr>
        <a href="{{ route('display.index') }}" target="_blank">
            <i class="fas fa-tv"></i> Display Ruang Tunggu
        </a>
        <a href="{{ route('kios.index') }}" target="_blank">
            <i class="fas fa-kiosk"></i> Kios Cetak Antrian
        </a>
        <a href="{{ route('petugas.loket.index') }}" target="_blank">
            <i class="fas fa-phone"></i> Loket Pemanggilan
        </a>
    </div>
    @endif
    @endauth

    <div class="{{ Auth::check() && Auth::user()->role === 'admin' ? 'main-content' : 'container' }}">
        @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        
        @yield('content')
    </div>

    <script>
        @yield('scripts')
    </script>
</body>
</html>