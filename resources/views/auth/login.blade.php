<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Antrian</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <style>
        /* Import Font */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');

        /* Reset & Base */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: #1e293b;
            background: #f8fafc;
        }

        /* Tata Letak Split Screen */
        .login-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr; /* 2 Kolom di Desktop */
            height: 100vh;
            width: 100%;
            overflow: hidden;
        }

        /* === 1. Kolom Kiri (Branding) === */
        .login-branding {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            color: white;
        }
        .brand-content {
            text-align: center;
        }
        .brand-content i {
            font-size: 60px;
            margin-bottom: 24px;
            text-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .brand-content h2 {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 10px 0;
        }
        .brand-content p {
            font-size: 16px;
            font-weight: 400;
            opacity: 0.8;
            max-width: 300px;
        }

        /* === 2. Kolom Kanan (Form) === */
        .login-form-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: #ffffff;
        }
        .login-form-container {
            width: 100%;
            max-width: 400px;
        }
        .login-form-container h2 {
            font-size: 28px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 8px 0;
        }
        .login-form-container .subtitle {
            font-size: 15px;
            color: #64748b;
            margin-bottom: 32px;
        }

        /* Alert (Konsisten dengan Admin Panel) */
        .alert {
            padding: 15px;
            margin-bottom: 24px;
            border-radius: 12px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-weight: 500;
            font-size: 14px;
        }
        .alert-danger {
            color: #991b1b;
            background-color: #fee2e2;
            border: 1px solid #fca5a5;
        }

        /* Form Group & Input Modern */
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 12px;
            color: #475569;
            margin-bottom: 6px;
        }
        .input-wrapper {
            position: relative;
        }
        .input-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 14px;
        }
        .form-group input {
            width: 100%;
            padding: 12px 14px 12px 40px; /* Padding kiri untuk ikon */
            border: 1px solid #e2e8f0; /* Warna border soft */
            border-radius: 10px; /* Rounded corners */
            font-size: 14px;
            color: #1e293b;
            background: #ffffff;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        /* Tombol Login Modern */
        .btn-login {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            margin-top: 10px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
        }

        /* Responsive */
        @media (max-width: 900px) {
            .login-wrapper {
                grid-template-columns: 1fr; /* Stack di mobile */
            }
            .login-branding {
                display: none; /* Sembunyikan branding di mobile */
            }
            .login-form-wrapper {
                background: #f8fafc; /* Latar abu-abu di mobile */
                align-items: flex-start;
                padding-top: 15%;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">

        <!-- Kolom Kiri (Branding - Tersembunyi di Mobile) -->
        <div class="login-branding">
            <div class="brand-content">
                <i class="fa-solid fa-hospital"></i>
                <h2>Antrian Ruang Coding</h2>
                <p>Manajemen antrian modern, cepat, dan efisien untuk pelayanan publik yang lebih baik.</p>
            </div>
        </div>

        <!-- Kolom Kanan (Form Login) -->
        <div class="login-form-wrapper">
            <div class="login-form-container">
                <h2>Selamat Datang</h2>
                <p class="subtitle">Login untuk melanjutkan ke dashboard.</p>

                <!-- Menampilkan Error -->
                @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
                @endif

                <!-- Menampilkan Error Validasi -->
                @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <label for="username">Username atau Email</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-user"></i>
                            <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus placeholder="Masukkan username">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" id="password" name="password" required placeholder="Masukkan password">
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>
            </div>
        </div>
        
    </div>
</body>
</html>