
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard | @yield('title')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- Include Modal Styles Component --}}
    @include('components.modal-styles')
    
    <style>
        /* BASE & RESET */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f4f7f6; color: #333; line-height: 1.6; }
        a { text-decoration: none; color: #3498db; }
        a:hover { color: #2980b9; }

        /* SIDEBAR / NAVIGATION */
        .sidebar { height: 100%; width: 250px; position: fixed; top: 0; left: 0; background-color: #2c3e50; padding-top: 20px; color: white; transition: 0.3s; z-index: 1000; overflow-y: auto; }
        .sidebar-header { text-align: center; margin-bottom: 30px; padding: 0 15px; }
        .sidebar-header h3 { font-size: 20px; font-weight: 700; border-bottom: 1px solid rgba(255, 255, 255, 0.1); padding-bottom: 10px; }
        .sidebar a { padding: 15px 15px; text-decoration: none; font-size: 16px; color: #ecf0f1; display: block; transition: 0.2s; border-left: 3px solid transparent; }
        .sidebar a:hover, .sidebar a.active { color: white; background-color: #34495e; border-left: 3px solid #3498db; }
        .sidebar a i { margin-right: 10px; width: 20px; text-align: center; }

        /* MAIN CONTENT */
        .main-content { margin-left: 250px; padding: 20px; transition: 0.3s; }
        .header-top { background: white; padding: 15px 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; border-radius: 8px; }
        .header-top h1 { font-size: 24px; color: #2c3e50; }
        .user-info { display: flex; align-items: center; gap: 15px; }
        .user-info .btn-logout { background-color: #e74c3c; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; transition: 0.2s; }
        .user-info .btn-logout:hover { background-color: #c0392b; }

        /* CARD & UTILITIES */
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .container-fluid { width: 100%; padding-right: 15px; padding-left: 15px; margin-right: auto; margin-left: auto; }

        /* RESPONSIVENESS */
        .menu-toggle { display: none; background: #3498db; color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 4px; position: fixed; top: 10px; left: 10px; z-index: 1001; }
        
        @media (max-width: 992px) {
            .sidebar { width: 0; padding-top: 60px; }
            .sidebar.active { width: 250px; }
            .main-content { margin-left: 0; }
            .main-content.active { margin-left: 250px; }
            .menu-toggle { display: block; }
        }

        /* SECTION STYLES */
        @yield('styles')
    </style>
    
</head>
<body>

    <div class="sidebar" id="mySidebar">
        <div class="sidebar-header">
            <h3>Antrian Admin</h3>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="{{ route('admin.loket.index') }}" class="{{ request()->routeIs('admin.loket.index') ? 'active' : '' }}">
            <i class="fas fa-door-open"></i> Manajemen Loket
        </a>
        <a href="{{ route('admin.layanan.index') }}" class="{{ request()->routeIs('admin.layanan.index') ? 'active' : '' }}">
            <i class="fas fa-concierge-bell"></i> Manajemen Layanan
        </a>
        <a href="{{ route('admin.antrian.index') }}" class="{{ request()->routeIs('admin.antrian.index') ? 'active' : '' }}">
            <i class="fas fa-list-ol"></i> Daftar Antrian
        </a>
        <a href="{{ route('admin.pusat-kontrol.index') }}" class="{{ request()->routeIs('admin.pusat-kontrol.index') ? 'active' : '' }}">
            <i class="fas fa-desktop"></i> Pusat Kontrol Pemanggilan
        </a>
        <a href="{{ route('admin.pengguna.index') }}" class="{{ request()->routeIs('admin.pengguna.index') ? 'active' : '' }}">
            <i class="fas fa-users-cog"></i> Manajemen Pengguna
        </a>
        <div style="border-top: 1px solid rgba(255, 255, 255, 0.1); margin: 15px 0;"></div>
        <a href="{{ route('admin.pengaturan.index') }}" class="{{ request()->routeIs('admin.pengaturan.index') ? 'active' : '' }}">
            <i class="fas fa-cogs"></i> Pengaturan Umum
        </a>
        <a href="{{ route('admin.advanced-settings.index') }}" class="{{ request()->routeIs('admin.advanced-settings.index') ? 'active' : '' }}">
            <i class="fas fa-wrench"></i> Pengaturan Lanjutan
        </a>
    </div>

    <button class="menu-toggle" onclick="toggleMenu()">
        <i class="fas fa-bars"></i>
    </button>
    <div class="main-content" id="mainContent">
        <div class="header-top">
            <h1 class="page-title">@yield('title')</h1>
            <div class="user-info">
                <span><i class="fas fa-user-circle"></i> {{ Auth::user()->name ?? 'Admin' }}</span>
                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </form>
            </div>
        </div>
        
        <div class="container-fluid">
            @yield('content')
        </div>
    </div>

    <script>
        // Toggle Sidebar on Mobile
        function toggleMenu() {
            const sidebar = document.getElementById('mySidebar');
            const mainContent = document.getElementById('mainContent');
            sidebar.classList.toggle('active');
            mainContent.classList.toggle('active');
        }

        // Script Section
        @yield('scripts')
    </script>
</body>
</html>