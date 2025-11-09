<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Antrian</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
        }
        .login-container { 
            background: white; 
            padding: 40px; 
            border-radius: 10px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.3); 
            width: 100%; 
            max-width: 400px; 
        }
        .login-header { text-align: center; margin-bottom: 30px; }
        .login-header i { font-size: 60px; color: #667eea; margin-bottom: 10px; }
        .login-header h2 { color: #333; margin-bottom: 5px; }
        .login-header p { color: #666; font-size: 14px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        .form-group input { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #ddd; 
            border-radius: 5px; 
            font-size: 14px; 
        }
        .form-group input:focus { outline: none; border-color: #667eea; }
        .btn-login { 
            width: 100%; 
            padding: 12px; 
            background: #667eea; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            font-size: 16px; 
            cursor: pointer; 
            font-weight: bold; 
        }
        .btn-login:hover { background: #5568d3; }
        .alert { 
            padding: 12px; 
            border-radius: 5px; 
            margin-bottom: 20px; 
            font-size: 14px; 
        }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fas fa-hospital"></i>
            <h2>Sistem Antrian Digital</h2>
            <p>Silakan login untuk melanjutkan</p>
        </div>

        @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label><i class="fas fa-user"></i> Username atau Email</label>
                <input type="text" name="username" value="{{ old('username') }}" required autofocus>
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
    </div>
</body>
</html>
